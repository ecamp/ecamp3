<?php

namespace App\Security\OAuth;

use App\Entity\Profile;
use App\Entity\User;
use App\OAuth\JWTStateOAuth2Client;
use App\Service\ClaimInvitationService;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
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

class GoogleAuthenticator extends OAuth2Authenticator {
    public function __construct(
        private readonly AuthenticationSuccessHandler $authenticationSuccessHandler,
        private readonly string $cookiePrefix,
        private readonly ClientRegistry $clientRegistry,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly JWTEncoderInterface $jwtDecoder,
        private readonly ClaimInvitationService $claimInvitationService,
    ) {}

    #[\Override]
    public function supports(Request $request): ?bool {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return 'connect_google_check' === $request->attributes->get('_route');
    }

    #[\Override]
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
                    $user = new User();
                    $user->profile = $profile;
                }

                $newlyActivatedUser = false;
                if (in_array($user->state, [null, User::STATE_NONREGISTERED, User::STATE_REGISTERED])) {
                    $newlyActivatedUser = true;
                    $user->state = User::STATE_ACTIVATED;
                }

                // persist user object
                $profile->googleId = $googleUser->getId();
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                if ($newlyActivatedUser) {
                    // Only after the user is persisted, claim invitations to make
                    // sure any errors during this process don't prevent user sign up
                    $this->claimInvitationService->claimInvitations($user, $email);
                }

                return $user;
            })
        );
    }

    #[\Override]
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

    #[\Override]
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new JsonResponse(['message' => $message, 'code' => Response::HTTP_FORBIDDEN], Response::HTTP_FORBIDDEN);
    }
}
