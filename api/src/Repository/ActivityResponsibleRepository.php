<?php

declare(strict_types=1);

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\ActivityResponsible;
use App\Entity\User;
use App\Util\QueryBuilderHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|ActivityResponsible find($id, $lockMode = null, $lockVersion = null)
 * @method null|ActivityResponsible findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityResponsible[]    findAll()
 * @method ActivityResponsible[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends ServiceEntityRepository<ActivityResponsible>
 */
class ActivityResponsibleRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ActivityResponsible::class);
    }

    #[\Override]
    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $activity = QueryBuilderHelper::findOrAddInnerRootJoinAlias($queryBuilder, $queryNameGenerator, 'activity');
        $this->filterByCampCollaboration($queryBuilder, $user, "{$activity}.camp");
    }
}
