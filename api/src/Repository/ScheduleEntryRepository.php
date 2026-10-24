<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\ScheduleEntry;
use App\Entity\User;
use App\Util\QueryBuilderHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|ScheduleEntry find($id, $lockMode = null, $lockVersion = null)
 * @method null|ScheduleEntry findOneBy(array $criteria, array $orderBy = null)
 * @method ScheduleEntry[]    findAll()
 * @method ScheduleEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends ServiceEntityRepository<ScheduleEntry>
 */
class ScheduleEntryRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ScheduleEntry::class);
    }

    #[\Override]
    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder {
        $qb = parent::createQueryBuilder($alias, $indexBy);
        $qb->orderBy($alias.'.period', \SortDirection::Ascending)
            ->addOrderBy($alias.'.startOffset', \SortDirection::Ascending)
            ->addOrderBy($alias.'.left', \SortDirection::Ascending)
            ->addOrderBy($alias.'.endOffset', \SortDirection::Descending)
            ->addOrderBy($alias.'.createTime', \SortDirection::Ascending)
            ->addOrderBy($alias.'.id', \SortDirection::Ascending)
        ;

        return $qb;
    }

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $activity = QueryBuilderHelper::findOrAddInnerRootJoinAlias($queryBuilder, $queryNameGenerator, 'activity');
        $this->filterByCampCollaboration($queryBuilder, $user, "{$activity}.camp");
    }
}
