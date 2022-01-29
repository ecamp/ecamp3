<?php

namespace App\Repository;

use App\Entity\ScheduleEntry;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|ScheduleEntry find($id, $lockMode = null, $lockVersion = null)
 * @method null|ScheduleEntry findOneBy(array $criteria, array $orderBy = null)
 * @method ScheduleEntry[]    findAll()
 * @method ScheduleEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleEntryRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ScheduleEntry::class);
    }

    public function createQueryBuilder($alias, $indexBy = null) {
        $qb = parent::createQueryBuilder($alias, $indexBy);
        $qb->orderBy($alias.'.period')
            ->addOrderBy($alias.'.periodOffset')
            ->addOrderBy($alias.'.id')
        ;

        return $qb;
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin("{$rootAlias}.activity", 'activity');
        $queryBuilder->innerJoin('activity.camp', 'camp');
        $this->filterByCampCollaboration($queryBuilder, $user);
    }
}
