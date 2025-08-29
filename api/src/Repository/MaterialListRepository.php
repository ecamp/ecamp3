<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Camp;
use App\Entity\MaterialList;
use App\Entity\User;
use App\Entity\UserCamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|MaterialList find($id, $lockMode = null, $lockVersion = null)
 * @method null|MaterialList findOneBy(array $criteria, array $orderBy = null)
 * @method MaterialList[]    findAll()
 * @method MaterialList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialListRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, MaterialList::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $campsQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        $campsQry->select('identity(uc.camp)');
        $campsQry->from(UserCamp::class, 'uc');
        $campsQry->where('uc.user = :current_user');

        $publicCampsQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        $publicCampsQry->select('c');
        $publicCampsQry->from(Camp::class, 'c');
        $publicCampsQry->where($queryBuilder->expr()->orX('c.isPrototype = true', 'c.isShared = true'));

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->andX(
                    "{$rootAlias}.campCollaboration IS NULL",
                    $queryBuilder->expr()->in("{$rootAlias}.camp", $publicCampsQry->getDQL())
                ),
                $queryBuilder->expr()->in("{$rootAlias}.camp", $campsQry->getDQL())
            )
        );
        $queryBuilder->setParameter('current_user', $user);
    }
}
