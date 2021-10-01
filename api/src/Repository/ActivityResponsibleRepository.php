<?php

namespace App\Repository;

use App\Entity\ActivityResponsible;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|ActivityResponsible find($id, $lockMode = null, $lockVersion = null)
 * @method null|ActivityResponsible findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityResponsible[]    findAll()
 * @method ActivityResponsible[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityResponsibleRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ActivityResponsible::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin("{$rootAlias}.activity", 'activity');
        $queryBuilder->innerJoin('activity.camp', 'camp');
        $this->filterByCampCollaboration($queryBuilder, $user);
    }
}
