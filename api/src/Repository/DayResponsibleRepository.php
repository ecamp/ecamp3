<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\DayResponsible;
use App\Entity\User;
use App\Util\QueryBuilderHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|DayResponsible find($id, $lockMode = null, $lockVersion = null)
 * @method null|DayResponsible findOneBy(array $criteria, array $orderBy = null)
 * @method DayResponsible[]    findAll()
 * @method DayResponsible[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DayResponsibleRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, DayResponsible::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $day = QueryBuilderHelper::findOrAddInnerRootJoinAlias($queryBuilder, $queryNameGenerator, 'day');
        $period = QueryBuilderHelper::findOrAddInnerJoinAlias($queryBuilder, $queryNameGenerator, $day, 'period');

        $queryBuilder->innerJoin("{$period}.camp", 'camp');
        $this->filterByCampCollaboration($queryBuilder, $user);
    }
}
