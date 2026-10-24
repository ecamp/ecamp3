<?php

declare(strict_types=1);

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\TypeInfo\Type;

/**
 * Boolean filter which only accepts the value true.
 *
 * Meant for boolean properties used as scoping_filters (see RequireCollectionFilterProvider):
 * filtering e.g. for isPrototype=false would satisfy the scoping requirement without actually
 * narrowing down the collection, so other values are rejected.
 */
final class IsTrueFilter extends AbstractFilter {
    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array {
        $description = [];
        foreach (array_keys($this->getProperties() ?? []) as $property) {
            $description[$property] = [
                'property' => $property,
                'type' => Type::bool()->__toString(),
                'required' => false,
                'description' => 'Only accepts the value true.',
                'schema' => ['type' => 'boolean', 'enum' => [true]],
            ];
        }

        return $description;
    }

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (!$this->isPropertyEnabled($property, $resourceClass) || !$this->isPropertyMapped($property, $resourceClass)) {
            return;
        }

        if ('true' !== $value) {
            throw new BadRequestHttpException("Filter {$property} only accepts the value true.");
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName($property);

        $queryBuilder->andWhere("{$rootAlias}.{$property} = :{$parameterName}");
        $queryBuilder->setParameter($parameterName, true);
    }
}
