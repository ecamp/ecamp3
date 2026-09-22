<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Util\RequestParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Rejects collection requests which are not scoped, usually to a single camp or to the current user.
 *
 * Every Doctrine backed collection operation has to declare which query filters scope it, using
 * extraProperties: ['scoping_filters' => ['camp', ...]]. At least one of those filters must be
 * present in the request. Collections which are inherently scoped, or which are not camp specific
 * at all, opt out using 'scoping_filters' => false.
 *
 * Operations which declare nothing are rejected, such that a newly added collection is unlistable
 * until somebody decides how it should be scoped.
 *
 * Sub-resource collections such as /camps/{campId}/activities are exempt: they are already scoped
 * by their uriVariables, whose Link security checks access to the parent resource.
 *
 * @template T of object
 *
 * @template-implements ProviderInterface<T>
 */
final readonly class RequireCollectionFilterProvider implements ProviderInterface {
    /**
     * @param ProviderInterface<T> $decorated
     */
    public function __construct(
        private ProviderInterface $decorated,
        private RequestStack $requestStack
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null {
        if ($operation instanceof CollectionOperationInterface && [] === $uriVariables) {
            $this->assertScoped($operation);
        }

        return $this->decorated->provide($operation, $uriVariables, $context);
    }

    private function assertScoped(Operation $operation): void {
        $scopingFilters = $operation->getExtraProperties()['scoping_filters'] ?? [];

        if (false === $scopingFilters) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $queryParameters = $this->getQueryParameters($request);

        foreach ($scopingFilters as $scopingFilter) {
            if (\array_key_exists($scopingFilter, $queryParameters)) {
                return;
            }
        }

        throw new BadRequestHttpException(
            [] === $scopingFilters
                ? 'This collection cannot be listed unfiltered.'
                : 'Filter on '.implode(' or ', $scopingFilters).' is required.'
        );
    }

    /**
     * Parses the query string the same way the filters themselves do, so that a filter which is
     * accepted here is a filter which actually gets applied. Reading $request->query instead would
     * see PHP's mangled parameter names, e.g. checklist_camp instead of checklist.camp.
     *
     * @return array<string, mixed>
     */
    private function getQueryParameters(Request $request): array {
        $queryParameters = $request->attributes->get('_api_query_parameters');

        if (null !== $queryParameters) {
            return $queryParameters;
        }

        $queryString = RequestParser::getQueryString($request);

        return $queryString ? RequestParser::parseRequestParams($queryString) : [];
    }
}
