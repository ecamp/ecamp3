<?php

namespace App\Repository;

use App\Entity\ContentNode\StoryboardSection;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @method null|StoryboardSection find($id, $lockMode = null, $lockVersion = null)
 * @method null|StoryboardSection findOneBy(array $criteria, array $orderBy = null)
 * @method StoryboardSection[]    findAll()
 * @method StoryboardSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryboardSectionRepository extends SortableServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByContentNode;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, StoryboardSection::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin("{$rootAlias}.storyboard", 'contentNode');

        $this->filterByContentNode($queryBuilder, $user, 'contentNode');
    }
}
