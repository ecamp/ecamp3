<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Activity;
use App\Entity\ScheduleEntry;
use App\Entity\User;
use App\Entity\UserCamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $activityQry = $this->getEntityManager()->createQueryBuilder();
        $activityQry->select('a');
        $activityQry->from(Activity::class, 'a');
        $activityQry->join(UserCamp::class, 'uc', Join::WITH, 'a.camp = uc.camp');
        $activityQry->where('uc.user = :current_user');

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere($queryBuilder->expr()->in("{$rootAlias}.activity", $activityQry->getDQL()));
        $queryBuilder->setParameter('current_user', $user);
    }
}
