<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Day;
use App\Entity\DayResponsible;
use App\Entity\User;
use App\Entity\UserCamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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
        $dayQry = $this->getEntityManager()->createQueryBuilder();
        $dayQry->select('d');
        $dayQry->from(Day::class, 'd');
        $dayQry->join('d.period', 'p');
        $dayQry->join(UserCamp::class, 'uc', Join::WITH, 'p.camp = uc.camp');
        $dayQry->where('uc.user = :current_user');

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere($queryBuilder->expr()->in("{$rootAlias}.day", $dayQry->getDQL()));
        $queryBuilder->setParameter('current_user', $user);
    }
}
