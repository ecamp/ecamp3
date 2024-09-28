<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Checklist;
use App\Entity\ChecklistItem;
use App\Entity\User;
use App\Entity\UserCamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|ChecklistItem find($id, $lockMode = null, $lockVersion = null)
 * @method null|ChecklistItem findOneBy(array $criteria, array $orderBy = null)
 * @method ChecklistItem[]    findAll()
 * @method ChecklistItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChecklistItemRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ChecklistItem::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $checklistQry = $this->getEntityManager()->createQueryBuilder();
        $checklistQry->select('c');
        $checklistQry->from(Checklist::class, 'c');
        $checklistQry->join(UserCamp::class, 'uc', Join::WITH, 'c.camp = uc.camp OR c.isPrototype = true');
        $checklistQry->where('uc.user = :current_user');

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere($queryBuilder->expr()->in("{$rootAlias}.checklist", $checklistQry->getDQL()));
        $queryBuilder->setParameter('current_user', $user);
    }
}
