<?php

namespace App\OAuth;

use App\Entity\OAuthState;
use App\Repository\OAuthStateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Exception\InvalidStateException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use LogicException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Modify the OAuth2Client such that it does not require native PHP sessions, which would have lots of implications
 * to user management in Symfony. Instead, we store the value of the OAuth state parameter inside a signed JWT token
 * in a HttpOnly cookie, and use that as a user-facing but non-forgeable alternative temporary session storage. The
 * value of the state parameter is available in the browser anyway (it's part of the URL when redirecting to the
 * external OAuth provider), so no secret server-only session storage is needed.
 * This is perfectly safe, even safer than our normal authentication flow: There we use the same mechanism but with a
 * longer-living token and with parts of the cookie available to JavaScript.
 */
class JWTStateOAuth2Client extends OAuth2Client implements OAuth2ClientInterface {
    public const JWT_TTL = 300; // seconds, i.e. 5 minutes of validity for the JWT token

    public function __construct(
        AbstractProvider $provider,
        private RequestStack $requestStack,
        private string $cookiePrefix,
        private string $appEnv,
        private JWTEncoderInterface $jwtEncoder,
        private EntityManagerInterface $entityManager,
        private OAuthStateRepository $stateRepository,
    ) {
        parent::__construct($provider, $requestStack);

        // Inform the original OAuth2 client implementation that there are no native PHP sessions in this
        // application; we will handle all session storage ourselves in this class here.
        $this->setAsStateless();
    }

    public static function getCookieName($cookiePrefix): string {
        return "{$cookiePrefix}oauth_state_jwt";
    }

    /**
     * Delegates to the original implementation to create a RedirectResponse. Then, adds a temporary cookie
     * to that response, containing a JWT token which will act our stateless session storage.
     *
     * The provider class has the option of passing custom data to also be stored inside the JWT token.
     * Caution: That data is publicly available in the browser! It's perfect though for storing a callback
     * URL for redirecting after the OAuth flow has completed.
     *
     * @throws \Exception
     */
    public function redirect(array $scopes = [], array $options = []): RedirectResponse {
        $state = bin2hex(random_bytes(16));
        $now = time();
        $expires = $now + self::JWT_TTL;

        $response = parent::redirect($scopes, array_merge($options, ['state' => $state]));

        try {
            $response->headers->setCookie(
                Cookie::create($this->getCookieName($this->cookiePrefix))
                    ->withValue($this->encodeStateJWT(array_merge($options['additionalData'] ?? [], [
                        'state' => $state,
                        'iat' => $now,
                        'exp' => $expires,
                    ])))
                    ->withHttpOnly()
                    ->withSameSite('lax')
                    ->withSecure('dev' !== $this->appEnv) // in local development, we don't use https
                    ->withExpires($expires)
            );
        } catch (JWTEncodeFailureException $e) {
            throw new LogicException('Could not create a JWT token for storing the state parameter securely');
        }

        $this->stateRepository->deleteAllExpiredBefore(new \DateTime('@'.time()));

        $oAuthState = new OAuthState();
        $oAuthState->state = $state;
        $oAuthState->expireTime = new \DateTime("@{$expires}");
        $this->entityManager->persist($oAuthState);
        $this->entityManager->flush();

        return $response;
    }

    /**
     * Checks the validity of the temporary JWT cookie, and checks that the state parameter is correct.
     * Any irregularities would indicate someone tampering with the login system (or someone taking longer
     * than 5 minutes to authenticate with the external service...)
     * After this custom state parameter check, we delegate to the original implementation to finish the OAuth
     * flow.
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getAccessToken(array $options = []): AccessTokenInterface {
        $jwt = $this->getCurrentRequest()->cookies->get($this->getCookieName($this->cookiePrefix));
        $actualState = $this->getCurrentRequest()->get('state');

        try {
            if ($this->decodeStateJWT($jwt) !== $actualState) {
                throw new InvalidStateException('Invalid state');
            }

            $now = new \DateTime('@'.time());
            $persistedState = $this->stateRepository->findOneUnexpiredBy($actualState, $now);
        } catch (JWTDecodeFailureException|NoResultException|NonUniqueResultException $e) {
            throw new InvalidStateException('Invalid state');
        }

        $this->entityManager->remove($persistedState);
        $this->entityManager->flush();

        return parent::getAccessToken($options);
    }

    /**
     * @param array $payload the payload to encode in the JWT token
     *
     * @return string the encoded JWT token
     *
     * @throws JWTEncodeFailureException
     */
    private function encodeStateJWT(array $payload): string {
        return $this->jwtEncoder->encode($payload);
    }

    /**
     * @param string $jwt the JWT token that should be checked for validity and decoded
     *
     * @return ?string the decoded state from the JWT token in the cookie
     *
     * @throws JWTDecodeFailureException
     */
    private function decodeStateJWT(string $jwt): ?string {
        return $this->jwtEncoder->decode($jwt)['state'] ?? null;
    }

    private function getCurrentRequest(): Request {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            throw new LogicException('There is no "current request", and it is needed to perform this action');
        }

        return $request;
    }
}
