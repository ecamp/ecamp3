<?php

namespace App\Security\OAuth;

use App\Entity\Profile;
use App\Entity\User;
use App\OAuth\HitobitoUser;
use App\OAuth\JWTStateOAuth2Client;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class HitobitoAuthenticator extends OAuth2Authenticator {
    public function __construct(
        private AuthenticationSuccessHandler $authenticationSuccessHandler,
        private string $cookiePrefix,
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private JWTEncoderInterface $jwtDecoder,
    ) {}

    public function supports(Request $request): ?bool {
        // continue ONLY if the current ROUTE matches one of the supported check ROUTES
        return 1 === preg_match('/^connect_(pbsmidata|cevidb|jubladb)_check$/', $request->attributes->get('_route'));
    }

    public function authenticate(Request $request): Passport {
        // extract provider from request path
        preg_match('/^\/auth\/(pbsmidata|cevidb|jubladb)\/callback$/', $request->getPathInfo(), $providerMatch);
        $provider = $providerMatch[1];

        /** @var OAuth2Client $client */
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
                $profiles = $profileRepository->findBy(['email' => strtolower($email)]);
                $profile = count($profiles) > 0 ? $profiles[0] : null;
                $user = $profile?->user;

                if (is_null($profile)) {
                    $profile = new Profile();
                    $profile->email = $email;
                    $profile->firstname = $hitobitoUser->getFirstName();
                    $profile->surname = $hitobitoUser->getLastName();
                    $profile->nickname = $hitobitoUser->getNickName();
                    $user = new User();
                    $user->profile = $profile;
                }

                if (in_array($user->state, [null, User::STATE_NONREGISTERED, User::STATE_REGISTERED])) {
                    $user->state = User::STATE_ACTIVATED;
                }

                // persist user object
                $profile->{"{$provider}Id"} = $hitobitoUser->getId();
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        $user = $this->security->getUser();
        $authSuccess = $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
        $redirectUrl = $this->jwtDecoder->decode($request->cookies->get(JWTStateOAuth2Client::getCookieName($this->cookiePrefix)))['callback'] ?? '/';

        $response = new RedirectResponse($redirectUrl);

        /** @var string[] $cookies */
        $cookies = $authSuccess->headers->all('set-cookie');
        $response->headers->set('set-cookie', $cookies);

        return $response;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new JsonResponse(['message' => $message, 'code' => Response::HTTP_FORBIDDEN], Response::HTTP_FORBIDDEN);
    }
}
