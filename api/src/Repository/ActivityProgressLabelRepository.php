<?php

namespace App\Repository;

use App\Entity\ActivityProgressLabel;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|ActivityProgressLabel find($id, $lockMode = null, $lockVersion = null)
 * @method null|ActivityProgressLabel findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityProgressLabel[]    findAll()
 * @method ActivityProgressLabel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityProgressLabelRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ActivityProgressLabel::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin("{$rootAlias}.camp", 'camp');
        $this->filterByCampCollaboration($queryBuilder, $user);
    }
}
