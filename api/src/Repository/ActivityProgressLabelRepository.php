<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\ActivityProgressLabel;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @method null|ActivityProgressLabel find($id, $lockMode = null, $lockVersion = null)
 * @method null|ActivityProgressLabel findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityProgressLabel[]    findAll()
 * @method ActivityProgressLabel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template T3 of ActivityProgressLabel
 *
 * @template-extends SortableServiceEntityRepository<T3>
 */
class ActivityProgressLabelRepository extends SortableServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    /**
     * @psalm-param class-string<T3> $entityClass
     */
    public function __construct(EntityManagerInterface $em, string $entityClass = ActivityProgressLabel::class) {
        parent::__construct($em, $entityClass);
    }

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $this->filterByCampCollaboration($queryBuilder, $user, "{$rootAlias}.camp");
    }
}
