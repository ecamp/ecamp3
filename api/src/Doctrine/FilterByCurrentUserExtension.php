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
use Symfony\Bundle\SecurityBundle\Security;

final readonly class FilterByCurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface {
    public function __construct(private Security $security, private EntityManagerInterface $entityManager) {}

    #[\Override]
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, ?string $resourceClass = null, ?Operation $operation = null, array $context = []): void {
        $extraProperties = $operation->getExtraProperties();
        if (array_key_exists('filter_by_current_user', $extraProperties) && false === $extraProperties['filter_by_current_user']) {
            return;
        }

        $this->addWhere($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    #[\Override]
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void {
        $this->addWhere($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass): void {
        $repository = $this->entityManager->getRepository($resourceClass);

        /** @var null|User $user */
        $user = $this->security->getUser();

        if (!($repository instanceof CanFilterByUserInterface) || null === $user) {
            return;
        }

        $repository->filterByUser($queryBuilder, $queryNameGenerator, $user);
    }
}
