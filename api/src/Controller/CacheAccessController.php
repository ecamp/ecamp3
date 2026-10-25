<?php

declare(strict_types=1);

namespace App\Controller;

use App\HttpCache\CacheScopeListener;
use App\Security\ResourceReadAccessChecker;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Tells the HTTP cache whether the current user may be served cached responses of a given scope.
 * The response itself is cacheable (per user and scope), but only if access is granted.
 */
class CacheAccessController {
    public const string ACCESS_HEADER = 'X-Cache-Access';
    public const int MAX_TTL_SECONDS = 43200;

    public function __construct(
        private readonly ResourceReadAccessChecker $accessChecker,
        private readonly Security $security,
    ) {}

    #[Route('/_cache_access', name: 'cache_access', methods: ['GET'])]
    public function __invoke(Request $request): Response {
        $response = new Response();

        $ttl = $this->isGranted((string) $request->headers->get(CacheScopeListener::HEADER)) ? $this->getTokenTtl() : 0;

        if ($ttl <= 0) {
            $response->headers->set(self::ACCESS_HEADER, 'denied');
            $response->setCache(['no_store' => true, 'private' => true]);

            return $response;
        }

        $response->headers->set(self::ACCESS_HEADER, 'granted');
        $response->setCache(['public' => true, 'max_age' => 0, 's_maxage' => $ttl]);

        return $response;
    }

    private function isGranted(string $scope): bool {
        try {
            return $this->accessChecker->check($scope)->granted;
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    /**
     * A grant must not outlive the JWT it was given for.
     */
    private function getTokenTtl(): int {
        $token = $this->security->getToken();
        $jwt = $token instanceof JWTPostAuthenticationToken ? $token->getCredentials() : null;
        $payload = is_string($jwt) ? explode('.', $jwt)[1] ?? '' : '';
        $expiresAt = json_decode(base64_decode(strtr($payload, '-_', '+/')), true)['exp'] ?? null;

        return is_int($expiresAt) ? min(self::MAX_TTL_SECONDS, $expiresAt - time()) : 0;
    }
}
