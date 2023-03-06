<?php

namespace App\Repository;

use App\Entity\ContentNode;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @method null|ContentNode find($id, $lockMode = null, $lockVersion = null)
 * @method null|ContentNode findOneBy(array $criteria, array $orderBy = null)
 * @method ContentNode[]    findAll()
 * @method ContentNode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template T3 of ContentNode
 *
 * @template-extends SortableServiceEntityRepository<T3>
 */
class ContentNodeRepository extends SortableServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByContentNode;

    /**
     * @psalm-param class-string<T3> $entityClass
     */
    public function __construct(EntityManagerInterface $em, string $entityClass = ContentNode::class) {
        parent::__construct($em, $entityClass);
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $this->filterByContentNode($queryBuilder, $user, $queryBuilder->getRootAliases()[0]);
    }
}
