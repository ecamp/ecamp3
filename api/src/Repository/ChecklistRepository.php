<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Checklist;
use App\Entity\User;
use App\Entity\UserCamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Checklist find($id, $lockMode = null, $lockVersion = null)
 * @method null|Checklist findOneBy(array $criteria, array $orderBy = null)
 * @method Checklist[]    findAll()
 * @method Checklist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChecklistRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Checklist::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $campsQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        $campsQry->select('identity(uc.camp)');
        $campsQry->from(UserCamp::class, 'uc');
        $campsQry->where('uc.user = :current_user');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                "{$rootAlias}.isPrototype = true",
                $queryBuilder->expr()->in("{$rootAlias}.camp", $campsQry->getDQL())
            )
        );
        $queryBuilder->setParameter('current_user', $user);
    }
}
