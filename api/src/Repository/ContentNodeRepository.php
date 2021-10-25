<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Category;
use App\Entity\ContentNode;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|ContentNode find($id, $lockMode = null, $lockVersion = null)
 * @method null|ContentNode findOneBy(array $criteria, array $orderBy = null)
 * @method ContentNode[]    findAll()
 * @method ContentNode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentNodeRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry, string $entityClass = ContentNode::class) {
        parent::__construct($registry, $entityClass);
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $this->buildContentNodeFilter($queryBuilder, $user, $queryBuilder->getRootAliases()[0]);
    }

    protected function buildContentNodeFilter(QueryBuilder $queryBuilder, User $user, string $contentNodeAlias): void {
        $queryBuilder->innerJoin("{$contentNodeAlias}.root", 'root');
        $queryBuilder->innerJoin('root.owner', 'owner');

        // add camp filter in case the ContentNode owner is an Activity
        $queryBuilder->leftJoin(Activity::class, 'activity', Join::WITH, 'activity.id = owner.id');
        $queryBuilder->leftJoin('activity.camp', 'camp_via_activity');
        $this->filterByCampCollaboration($queryBuilder, $user, 'camp_via_activity');

        // add camp filter in case the ContentNode owner is a category
        $queryBuilder->leftJoin(Category::class, 'category', Join::WITH, 'category.id = owner.id');
        $queryBuilder->leftJoin('category.camp', 'camp_via_category');
        $this->filterByCampCollaboration($queryBuilder, $user, 'camp_via_category');
    }
}
