<?php

namespace App\Repository;

use App\Entity\MaterialItem;
use App\Entity\User;
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

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin("{$rootAlias}.materialList", 'materialList');
        $queryBuilder->innerJoin('materialList.camp', 'camp');
        $this->filterByCampCollaboration($queryBuilder, $user);
    }
}
