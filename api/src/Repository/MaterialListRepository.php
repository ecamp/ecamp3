<?php

namespace App\Repository;

use App\Entity\MaterialList;
use App\Entity\User;
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
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, MaterialList::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin("{$rootAlias}.camp", 'camp');
        $this->filterByCampCollaboration($queryBuilder, $user);
    }
}
