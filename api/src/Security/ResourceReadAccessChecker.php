<?php

declare(strict_types=1);

namespace App\Security;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Exception\ItemNotFoundException;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\ResourceAccessCheckerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Decides whether the current user may read (GET) the resource behind a resource IRI, by evaluating the
 * security rules that are declared on the GET operation of that resource.
 */
class ResourceReadAccessChecker {
    public function __construct(
        // API Platform's router matches independent of the method of the current request and strips the base URL
        #[Autowire(service: 'api_platform.router')]
        private readonly RouterInterface $router,
        private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataFactory,
        private readonly ResourceAccessCheckerInterface $resourceAccessChecker,
        private readonly IriConverterInterface $iriConverter,
        private readonly ManagerRegistry $managerRegistry,
    ) {}

    /**
     * @param string $iri path of a canonical resource IRI, including the base URL (such as /api) if there is one
     *
     * @throws \InvalidArgumentException if the IRI does not reference an existing, readable-by-GET resource or collection
     */
    public function check(string $iri): ResourceReadAccess {
        if (str_contains($iri, '?') || str_contains($iri, '#')) {
            throw new \InvalidArgumentException('Only canonical resource IRIs without query string or fragment are supported.');
        }

        try {
            $parameters = $this->router->match($iri);
        } catch (RoutingException) {
            throw new \InvalidArgumentException('No route matches the given IRI.');
        }

        $resourceClass = $parameters['_api_resource_class'] ?? null;
        $operationName = $parameters['_api_operation_name'] ?? null;
        if (!is_string($resourceClass) || !is_string($operationName)) {
            throw new \InvalidArgumentException('The given IRI does not reference a resource.');
        }

        $operation = $this->resourceMetadataFactory->create($resourceClass)->getOperation($operationName);
        if (!$operation instanceof HttpOperation || Request::METHOD_GET !== $operation->getMethod()) {
            throw new \InvalidArgumentException('The given IRI does not reference a readable resource.');
        }

        $granted = $operation instanceof CollectionOperationInterface
            ? $this->isCollectionReadable($operation, $parameters)
            : $this->isItemReadable($operation, $iri);

        return new ResourceReadAccess($granted, $iri, $resourceClass, $operation);
    }

    private function isItemReadable(HttpOperation $operation, string $iri): bool {
        try {
            // Also runs the security checks of the state provider, if there are any
            $object = $this->iriConverter->getResourceFromIri($iri);
        } catch (ItemNotFoundException) {
            throw new \InvalidArgumentException('The resource does not exist.');
        } catch (HttpExceptionInterface $e) {
            // The state provider denies access with a 403 (or hides the resource with a 404)
            if (in_array($e->getStatusCode(), [403, 404], true)) {
                return false;
            }

            throw $e;
        }

        $security = $operation->getSecurity();

        return null === $security
            || $this->resourceAccessChecker->isGranted($operation->getClass(), $security, ['object' => $object]);
    }

    private function isCollectionReadable(HttpOperation $operation, array $routeParameters): bool {
        // Mirrors ApiPlatform\State\Provider\SecurityParameterProvider, which does the same when serving the request
        foreach ($operation->getUriVariables() ?? [] as $parameterName => $link) {
            if (!$link instanceof Link || null === $link->getSecurity()) {
                continue;
            }

            $fromClass = $link->getFromClass();
            $linked = null === $fromClass || !isset($routeParameters[$parameterName])
                ? null
                : $this->managerRegistry->getManagerForClass($fromClass)?->find($fromClass, $routeParameters[$parameterName]);
            if (null === $linked) {
                throw new \InvalidArgumentException('The parent resource does not exist.');
            }

            $objectName = $link->getSecurityObjectName() ?? $link->getToProperty() ?? $link->getFromProperty();
            $variables = $objectName ? [$objectName => $linked] : [];
            if (!$this->resourceAccessChecker->isGranted($link->getFromClass() ?? $operation->getClass(), $link->getSecurity(), $variables)) {
                return false;
            }
        }

        $security = $operation->getSecurity();

        return null === $security
            || $this->resourceAccessChecker->isGranted($operation->getClass(), $security);
    }
}
