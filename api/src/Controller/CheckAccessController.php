<?php

declare(strict_types=1);

namespace App\Controller;

use App\HttpCache\JwtLifetime;
use App\Security\ResourceReadAccessChecker;
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

    public function __construct(
        private readonly ResourceReadAccessChecker $accessChecker,
        private readonly JwtLifetime $jwtLifetime,
    ) {}

    #[Route('/authorization/check_access', name: self::ROUTE, methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse {
        $iri = $request->query->all()['iri'] ?? null;
        if (!is_string($iri) || '' === $iri) {
            return new JsonResponse(['error' => "Query parameter 'iri' is required"], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->isGranted($iri)) {
            // Not found, like the resource itself: users must not tell missing from forbidden resources
            $response = new JsonResponse(['granted' => false, 'iri' => $iri], Response::HTTP_NOT_FOUND);
            $response->setPrivate();

            return $response;
        }

        $response = new JsonResponse(['granted' => true, 'iri' => $iri], Response::HTTP_OK);
        $response->setCache(['s_maxage' => $this->jwtLifetime->getCacheTtl()]);

        return $response;
    }

    private function isGranted(string $iri): bool {
        try {
            return $this->accessChecker->check($iri)->granted;
        } catch (\InvalidArgumentException) {
            return false;
        }
    }
}
