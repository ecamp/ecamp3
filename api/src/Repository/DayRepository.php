<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Day;
use App\Entity\Period;
use App\Entity\User;
use App\Entity\UserCamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Day find($id, $lockMode = null, $lockVersion = null)
 * @method null|Day findOneBy(array $criteria, array $orderBy = null)
 * @method Day[]    findAll()
 * @method Day[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DayRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Day::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $periodQry = $this->getEntityManager()->createQueryBuilder();
        $periodQry->select('p');
        $periodQry->from(Period::class, 'p');
        $periodQry->join(UserCamp::class, 'uc', Join::WITH, 'p.camp = uc.camp');
        $periodQry->where('uc.user = :current_user');

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere($queryBuilder->expr()->in("{$rootAlias}.period", $periodQry->getDQL()));
        $queryBuilder->setParameter('current_user', $user);
    }
}
