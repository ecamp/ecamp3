<?php

namespace App\Security\OAuth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class HitobitoAuthenticator extends OAuth2Authenticator {
    public function __construct(
        private AuthenticationSuccessHandlerInterface $authenticationSuccessHandler,
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $entityManager,
        private RouterInterface $router,
        private Security $security,
    ) {
    }

    public function supports(Request $request): ?bool {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return 'connect_hitobito_check' === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): Passport {
        $client = $this->clientRegistry->getClient('hitobito');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var HitobitoUser $hitobitoUser */
                $hitobitoUser = $client->fetchUserFromToken($accessToken);

                $email = $hitobitoUser->getEmail();

                $userRepository = $this->entityManager->getRepository(User::class);

                // has the user logged in with Hitobito before?
                $existingUser = $userRepository->findOneBy(['hitobitoId' => $hitobitoUser->getId()]);

                if ($existingUser) {
                    return $existingUser;
                }

                // do we have a matching user by email?
                $user = $userRepository->findOneBy(['email' => $email]);

                if (is_null($user)) {
                    $user = new User();
                    $user->email = $email;
                    $user->firstname = $hitobitoUser->getFirstName();
                    $user->surname = $hitobitoUser->getLastName();
                    $user->nickname = $hitobitoUser->getNickName();
                    $user->username = $email;
                }

                // persist user object
                $user->hitobitoId = $hitobitoUser->getId();
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        $user = $this->security->getUser();
        $authSuccess = $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
        $redirectUrl = $request->getSession()->get('redirect_uri');

        $response = new RedirectResponse($redirectUrl);
        $response->headers->set('set-cookie', $authSuccess->headers->all()['set-cookie']);

        return $response;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
}
