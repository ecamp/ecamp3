<?php

namespace App\Repository;

use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Camp find($id, $lockMode = null, $lockVersion = null)
 * @method null|Camp findOneBy(array $criteria, array $orderBy = null)
 * @method Camp[]    findAll()
 * @method Camp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Camp::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->leftJoin("{$rootAlias}.collaborations", 'campCollaboration');
        $queryBuilder->andWhere("(campCollaboration.user = :current_user and campCollaboration.status = :established) or {$rootAlias}.isPrototype = :true");
        $queryBuilder->setParameter('current_user', $user->getId());
        $queryBuilder->setParameter('established', CampCollaboration::STATUS_ESTABLISHED);
        $queryBuilder->setParameter('true', true);
    }
}
