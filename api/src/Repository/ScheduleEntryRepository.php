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

    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder {
        $qb = parent::createQueryBuilder($alias, $indexBy);
        $qb->orderBy($alias.'.period', 'ASC')
            ->addOrderBy($alias.'.startOffset', 'ASC')
            ->addOrderBy($alias.'.left', 'ASC')
            ->addOrderBy($alias.'.endOffset', 'DESC')
            ->addOrderBy($alias.'.id', 'ASC')
        ;

        return $qb;
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin("{$rootAlias}.activity", 'activity');
        $this->filterByCampCollaboration($queryBuilder, $user, 'activity.camp');
    }
}
