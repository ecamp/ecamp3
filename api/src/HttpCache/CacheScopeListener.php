<?php

declare(strict_types=1);

namespace App\HttpCache;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\BelongsToCampInterface;
use App\Entity\Camp;
use App\Entity\Period;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Tells the HTTP cache which read access is required to be served a response: Operations with the extra
 * property "cache_scope" => "camp" require read access to the camp of the response, operations with
 * "cache_scope" => "public" require no access. Responses without a scope must not be shared between users.
 */
final class CacheScopeListener {
    public const string HEADER = 'X-Cache-Scope';
    public const string EXTRA_PROPERTY = 'cache_scope';
    public const string SCOPE_CAMP = 'camp';
    public const string SCOPE_PUBLIC = 'public';

    public function __construct(
        private readonly CacheScopeResolver $cacheScopeResolver,
        private readonly IriConverterInterface $iriConverter,
        private readonly EntityManagerInterface $em,
    ) {}

    public function onKernelResponse(ResponseEvent $event): void {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (!$request->isMethod(Request::METHOD_GET) || Response::HTTP_OK !== $response->getStatusCode()) {
            return;
        }

        $scope = $this->getScope($request);

        if (null !== $scope) {
            $response->headers->set(self::HEADER, $scope);
        }
    }

    private function getScope(Request $request): ?string {
        return match ($this->cacheScopeResolver->getDeclaredScope($request)) {
            self::SCOPE_PUBLIC => self::SCOPE_PUBLIC,
            self::SCOPE_CAMP => $this->getCampIri($request, $this->isCollection($request)),
            default => null,
        };
    }

    private function isCollection(Request $request): bool {
        return $this->cacheScopeResolver->getOperation($request) instanceof CollectionOperationInterface;
    }

    private function getCampIri(Request $request, bool $isCollection): ?string {
        $camp = $isCollection ? $this->getCampOfCollection($request) : $this->getCampOfItem($request);

        return $camp ? $this->iriConverter->getIriFromResource($camp) : null;
    }

    private function getCampOfItem(Request $request): ?Camp {
        $data = $request->attributes->get('data');

        return $data instanceof BelongsToCampInterface ? $data->getCamp() : null;
    }

    private function getCampOfCollection(Request $request): ?Camp {
        if ($campId = $request->attributes->get('campId')) {
            return $this->em->find(Camp::class, $campId);
        }

        if ($periodId = $request->attributes->get('periodId')) {
            return $this->em->find(Period::class, $periodId)?->getCamp();
        }

        return null;
    }
}
