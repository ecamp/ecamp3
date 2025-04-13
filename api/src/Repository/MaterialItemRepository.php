<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\MaterialItem;
use App\Entity\User;
use App\Util\QueryBuilderHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|MaterialItem find($id, $lockMode = null, $lockVersion = null)
 * @method null|MaterialItem findOneBy(array $criteria, array $orderBy = null)
 * @method MaterialItem[]    findAll()
 * @method MaterialItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialItemRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, MaterialItem::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $materialList = QueryBuilderHelper::findOrAddInnerRootJoinAlias($queryBuilder, $queryNameGenerator, 'materialList');
        $this->filterByCampCollaboration($queryBuilder, $user, "{$materialList}.camp");
    }
}
