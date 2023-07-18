<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Repository\CanFilterByCampInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class FilterByCampExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass = null, Operation $operation = null, array $context = []): void {
        if (!isset($context['uri_variables']['campId'])) {
            return;
        }

        $this->addWhere($queryBuilder, $queryNameGenerator, $resourceClass, $context['uri_variables']['campId']);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void {
        if (!isset($context['uri_variables']['campId'])) {
            return;
        }

        $this->addWhere($queryBuilder, $queryNameGenerator, $resourceClass, $context['uri_variables']['campId']);
    }

    private function addWhere(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $campId): void {
        $repository = $this->entityManager->getRepository($resourceClass);

        if (!($repository instanceof CanFilterByCampInterface) || null === $campId) {
            return;
        }

        $repository->filterByCamp($queryBuilder, $queryNameGenerator, $campId);
    }
}
