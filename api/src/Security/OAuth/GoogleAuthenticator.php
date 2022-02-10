<?php

namespace App\Security\OAuth;

use App\Entity\Profile;
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

class GoogleAuthenticator extends OAuth2Authenticator {
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
        return 'connect_google_check' === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): Passport {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                $email = $googleUser->getEmail();

                $profileRepository = $this->entityManager->getRepository(Profile::class);

                // has the user logged in with Google before?
                $existingProfile = $profileRepository->findOneBy(['googleId' => $googleUser->getId()]);

                if ($existingProfile) {
                    return $existingProfile->user;
                }

                // do we have a matching user by email?
                $profile = $profileRepository->findOneBy(['email' => $email]);
                $user = $profile?->user;

                if (is_null($profile)) {
                    $profile = new Profile();
                    $profile->email = $email;
                    $profile->firstname = $googleUser->getFirstName();
                    $profile->surname = $googleUser->getLastName();
                    $profile->username = $email;
                    $user = new User();
                    $user->profile = $profile;
                    $user->state = User::STATE_ACTIVATED;
                }

                // persist user object
                $user->googleId = $googleUser->getId();
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
