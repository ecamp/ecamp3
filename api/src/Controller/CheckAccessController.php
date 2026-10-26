<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\ResourceReadAccessChecker;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Tells whether the current user may read the resource with the given IRI. The HTTP cache uses this to decide whether
 * a cached response, which delegates its security to the resource, may be served to the user.
 * A granted answer is cacheable (per user and IRI), but not longer than the login of the user.
 */
class CheckAccessController {
    public const string ROUTE = 'check_access';
    public const int MAX_TTL_SECONDS = 43200;

    public function __construct(
        private readonly ResourceReadAccessChecker $accessChecker,
        private readonly Security $security,
    ) {}

    #[Route('/authorization/check_access', name: self::ROUTE, methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse {
        $iri = $request->query->all()['iri'] ?? null;
        if (!is_string($iri) || '' === $iri) {
            return new JsonResponse(['error' => "Query parameter 'iri' is required"], Response::HTTP_BAD_REQUEST);
        }

        $ttl = $this->isGranted($iri) ? $this->getTokenTtl() : 0;

        if ($ttl <= 0) {
            $response = new JsonResponse(['granted' => false, 'iri' => $iri], Response::HTTP_FORBIDDEN);
            $response->setCache(['no_store' => true, 'private' => true]);

            return $response;
        }

        $response = new JsonResponse(['granted' => true, 'iri' => $iri], Response::HTTP_OK);
        $response->setCache(['public' => true, 'max_age' => 0, 's_maxage' => $ttl]);

        return $response;
    }

    private function isGranted(string $iri): bool {
        try {
            return $this->accessChecker->check($iri)->granted;
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
