<?php

namespace App\Repository;

use App\Entity\ContentNode\MultiSelectOption;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @method null|MultiSelectOption find($id, $lockMode = null, $lockVersion = null)
 * @method null|MultiSelectOption findOneBy(array $criteria, array $orderBy = null)
 * @method MultiSelectOption[]    findAll()
 * @method MultiSelectOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MultiSelectOptionRepository extends SortableServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByContentNode;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, MultiSelectOption::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin("{$rootAlias}.multiSelect", 'contentNode');

        $this->filterByContentNode($queryBuilder, $user, 'contentNode');
    }
}
