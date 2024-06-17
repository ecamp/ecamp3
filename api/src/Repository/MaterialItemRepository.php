<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\MaterialItem;
use App\Entity\MaterialList;
use App\Entity\User;
use App\Entity\UserCamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|MaterialItem find($id, $lockMode = null, $lockVersion = null)
 * @method null|MaterialItem findOneBy(array $criteria, array $orderBy = null)
 * @method MaterialItem[]    findAll()
 * @method MaterialItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialItemRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, MaterialItem::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $materialListQry = $this->getEntityManager()->createQueryBuilder();
        $materialListQry->select('ml');
        $materialListQry->from(MaterialList::class, 'ml');
        $materialListQry->join(UserCamp::class, 'uc', Join::WITH, 'ml.camp = uc.camp');
        $materialListQry->where('uc.user = :current_user');

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere($queryBuilder->expr()->in("{$rootAlias}.materialList", $materialListQry->getDQL()));
        $queryBuilder->setParameter('current_user', $user);
    }
}
