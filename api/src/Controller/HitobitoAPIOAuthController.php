<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\OAuth\JWTStateOAuth2Client;
use App\Service\Hitobito\AccessTokenProvider;
use App\Service\Hitobito\HitobitoProvider;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Exception\InvalidStateException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Implements the OAuth flow used to authorize the current eCamp user with a Hitobito provider
 * (such as MiData) for access to the Hitobito API. This is intentionally separate from the
 * regular OAuth login flow, so we can request additional scopes and also allow the user to utilize
 * different Hitobito accounts with reauthentication.
 *
 * Note: The callback endpoint must not require authentication. This is because the authentication cookies (`jwt_hp` and `jwt_s`)
 * have SameSite=Strict, meaning that we cannot read them when the user is redirected back to the callback endpoint.
 * To obtain the eCamp user id during the callback, we store it in the additional data alongside the callback URL.
 */
class HitobitoAPIOAuthController extends AbstractController {
    /**
     * Callback URLs that the frontend is allowed to request being redirected back to once the
     * OAuth flow has completed. "%s" is replaced with the requested provider.
     */
    private const array ALLOWED_CALLBACK_PATTERNS = [
        '#^/camps/hitobito/%s/import$#',
        '#^/camps/[^/]+/[^/]+/hitobito/sync$#',
        '#^/camps/[^/]+/[^/]+/hitobito/invite$#',
        '#^/camps/hitobito/%s/[^/]+$#',
    ];

    public function __construct(
        private readonly string $cookiePrefix,
        private readonly string $frontendBaseUrl,
        private readonly array $scopes,
        private readonly ClientRegistry $clientRegistry,
        private readonly JWTEncoderInterface $jwtDecoder,
        private readonly AccessTokenProvider $accessTokenProvider,
    ) {}

    /**
     * Starts the OAuth flow: redirects the user to the given Hitobito provider, so they can
     * authorize eCamp so we can access their events on their behalf.
     */
    #[Route('/hitobito/{provider}/oauth', name: 'hitobito_oauth_start', methods: ['GET'])]
    public function start(string $provider, Request $request): RedirectResponse {
        $provider = HitobitoProvider::parse($provider);

        $callback = $request->query->get('callback', '');
        $this->assertCallbackAllowed($provider, $callback);

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('This route requires an authenticated eCamp user');
        }

        return $this->getClient($provider)->redirect($this->scopes, [
            'additionalData' => ['callback' => $callback, 'eCampUserId' => $user->getId()],
        ]);
    }

    /**
     * Called by the Hitobito provider after the user has granted (or denied) access. Stores the
     * resulting access token in a cookie, and redirects back to the frontend.
     */
    #[Route('/hitobito/{provider}/oauth/callback', name: 'hitobito_oauth_callback', methods: ['GET'])]
    public function callback(string $provider, Request $request): RedirectResponse {
        $provider = HitobitoProvider::parse($provider);

        $client = $this->getClient($provider);

        try {
            $accessToken = $client->getAccessToken();
            $hitobitoUserId = $client->fetchUserFromToken($accessToken)->getId();
            $additionalData = $this->getAdditionalData($request);
        } catch (IdentityProviderException|InvalidStateException|JWTDecodeFailureException $e) {
            // Invalid/expired state, or the user rejected the authorization request
            // Redirect back to the frontend
            return new RedirectResponse($this->frontendBaseUrl);
        }

        if (!isset($additionalData['callback']) || !isset($additionalData['eCampUserId'])) {
            throw new BadRequestHttpException('Additional data is incorrectly formatted');
        }
        $this->assertCallbackAllowed($provider, $additionalData['callback']);

        $response = new RedirectResponse($additionalData['callback']);
        $response->headers->setCookie(
            $this->accessTokenProvider->createCookie($provider, $additionalData['eCampUserId'], $hitobitoUserId, $accessToken)
        );

        return $response;
    }

    private function getClient(HitobitoProvider $provider): OAuth2ClientInterface {
        return $this->clientRegistry->getClient("{$provider->value}_hitobito_api");
    }

    /**
     * Returns the additional data from the OAuth JWT state cookie.
     *
     * @throws JWTDecodeFailureException
     */
    private function getAdditionalData(Request $request): array {
        $jwt = $request->cookies->get(JWTStateOAuth2Client::getCookieName($this->cookiePrefix));

        return $this->jwtDecoder->decode($jwt);
    }

    private function assertCallbackAllowed(HitobitoProvider $provider, string $callback): void {
        foreach (self::ALLOWED_CALLBACK_PATTERNS as $pattern) {
            if (1 === preg_match(sprintf($pattern, preg_quote($provider->value, '#')), $callback)) {
                return;
            }
        }

        throw new BadRequestHttpException('Given callback is not allowed');
    }
}
