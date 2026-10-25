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
 * Tells the HTTP cache which read access is required to be served a response. Operations delegate their security to
 * a resource with the extra property "cache_security_delegate": "camp" requires read access to the camp of the
 * response, "public" requires no access. Responses without a delegate must not be shared between users.
 */
final class CacheSecurityDelegateListener {
    public const string HEADER = 'X-Cache-Security-Delegate';
    public const string EXTRA_PROPERTY = 'cache_security_delegate';
    public const string DELEGATE_CAMP = 'camp';
    public const string DELEGATE_PUBLIC = 'public';

    public function __construct(
        private readonly CacheSecurityDelegateResolver $cacheSecurityDelegateResolver,
        private readonly IriConverterInterface $iriConverter,
        private readonly EntityManagerInterface $em,
    ) {}

    public function onKernelResponse(ResponseEvent $event): void {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (!$request->isMethod(Request::METHOD_GET) || Response::HTTP_OK !== $response->getStatusCode()) {
            return;
        }

        $delegate = $this->getDelegate($request);

        if (null !== $delegate) {
            $response->headers->set(self::HEADER, $delegate);
        }
    }

    private function getDelegate(Request $request): ?string {
        return match ($this->cacheSecurityDelegateResolver->getDeclaredDelegate($request)) {
            self::DELEGATE_PUBLIC => self::DELEGATE_PUBLIC,
            self::DELEGATE_CAMP => $this->getCampIri($request, $this->isCollection($request)),
            default => null,
        };
    }

    private function isCollection(Request $request): bool {
        return $this->cacheSecurityDelegateResolver->getOperation($request) instanceof CollectionOperationInterface;
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
