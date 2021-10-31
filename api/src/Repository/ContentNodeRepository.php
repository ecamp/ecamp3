<?php

namespace App\Repository;

use App\Entity\ContentNode;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|ContentNode find($id, $lockMode = null, $lockVersion = null)
 * @method null|ContentNode findOneBy(array $criteria, array $orderBy = null)
 * @method ContentNode[]    findAll()
 * @method ContentNode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentNodeRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByContentNode;

    public function __construct(ManagerRegistry $registry, string $entityClass = ContentNode::class) {
        parent::__construct($registry, $entityClass);
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $this->filterByContentNode($queryBuilder, $user, $queryBuilder->getRootAliases()[0]);
    }
}
