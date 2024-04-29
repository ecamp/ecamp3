<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Activity;
use App\Entity\ActivityResponsible;
use App\Entity\User;
use App\Entity\UserCamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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
