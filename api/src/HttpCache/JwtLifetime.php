<?php

declare(strict_types=1);

namespace App\HttpCache;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Cached responses that depend on the login must not outlive the JWT they were created for.
 */
class JwtLifetime {
    public const int MAX_TTL_SECONDS = 43200;

    public function __construct(private readonly Security $security) {}

    /**
     * @return int seconds a response of the current login may be cached, 0 if it must not be
     */
    public function getCacheTtl(): int {
        $token = $this->security->getToken();
        $jwt = $token instanceof JWTPostAuthenticationToken ? $token->getCredentials() : null;
        $payload = is_string($jwt) ? explode('.', $jwt)[1] ?? '' : '';
        $expiresAt = json_decode(base64_decode(strtr($payload, '-_', '+/')), true)['exp'] ?? null;

        return is_int($expiresAt) ? max(0, min(self::MAX_TTL_SECONDS, $expiresAt - time())) : 0;
    }
}
