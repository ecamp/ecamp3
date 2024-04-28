<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
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

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $this->filterByCampCollaboration($queryBuilder, $user, "{$rootAlias}.camp");
    }
}
