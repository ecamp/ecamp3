<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use App\Repository\CanFilterByUserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class FilterByCurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface {
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager) {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass = null, Operation $operation = null, array $context = []): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void {
        $repository = $this->entityManager->getRepository($resourceClass);

        /** @var null|User $user */
        $user = $this->security->getUser();

        if (!($repository instanceof CanFilterByUserInterface) || null === $user) {
            return;
        }

        $repository->filterByUser($queryBuilder, $user);
    }
}
