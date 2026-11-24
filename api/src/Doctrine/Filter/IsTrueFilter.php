<?php

declare(strict_types=1);

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\BackwardCompatibleFilterDescriptionTrait;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Boolean filter which only accepts the value true.
 *
 * Meant for boolean properties used as scoping_filters (see RequireCollectionFilterProvider):
 * filtering e.g. for isPrototype=false would satisfy the scoping requirement without actually
 * narrowing down the collection, so other values are rejected.
 */
final class IsTrueFilter implements FilterInterface, OpenApiParameterFilterInterface {
    use BackwardCompatibleFilterDescriptionTrait;

    public function getOpenApiParameters(Parameter $parameter): OpenApiParameter {
        return new OpenApiParameter(
            name: $parameter->getKey(),
            in: 'query',
            description: 'Only accepts the value true.',
            schema: ['type' => 'boolean', 'enum' => [true]]
        );
    }

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $property = $context['parameter']->getProperty();
        $value = $context['parameter']->getValue();

        if ('true' !== $value) {
            throw new BadRequestHttpException("Filter {$property} only accepts the value true.");
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName($property);

        $queryBuilder->andWhere("{$rootAlias}.{$property} = :{$parameterName}");
        $queryBuilder->setParameter($parameterName, true);
    }
}
