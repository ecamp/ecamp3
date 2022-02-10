<?php

namespace App\Security\OAuth;

use App\Entity\Profile;
use App\Entity\User;
use App\OAuth\Hitobito;
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
        // continue ONLY if the current ROUTE matches one of the supported check ROUTES
        return preg_match('/^connect_(pbsmidata|cevidb)_check$/', $request->attributes->get('_route'));
    }

    public function authenticate(Request $request): Passport {
        // extract provider from request path
        preg_match('/^\/auth\/(pbsmidata|cevidb)\/callback$/', $request->getPathInfo(), $providerMatch);
        $provider = $providerMatch[1];
        /** @var Hitobito $client */
        $client = $this->clientRegistry->getClient($provider);
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client, $provider) {
                /** @var HitobitoUser $hitobitoUser */
                $hitobitoUser = $client->fetchUserFromToken($accessToken);

                $email = $hitobitoUser->getEmail();

                $profileRepository = $this->entityManager->getRepository(Profile::class);

                // has the user logged in with Hitobito before?
                $existingProfile = $profileRepository->findOneBy(["{$provider}Id" => $hitobitoUser->getId()]);

                if ($existingProfile) {
                    return $existingProfile->user;
                }

                // do we have a matching user by email?
                $profile = $profileRepository->findOneBy(['email' => $email]);
                $user = $profile?->user;

                if (is_null($profile)) {
                    $profile = new Profile();
                    $profile->email = $email;
                    $profile->firstname = $hitobitoUser->getFirstName();
                    $profile->surname = $hitobitoUser->getLastName();
                    $profile->nickname = $hitobitoUser->getNickName();
                    $profile->username = $email;
                    $user = new User();
                    $user->profile = $profile;
                    $user->state = User::STATE_ACTIVATED;
                }

                // persist user object
                $user->{"${provider}Id"} = $hitobitoUser->getId();
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
