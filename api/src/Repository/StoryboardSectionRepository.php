<?php

namespace App\Repository;

use App\Entity\ContentNode\StoryboardSection;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|StoryboardSection find($id, $lockMode = null, $lockVersion = null)
 * @method null|StoryboardSection findOneBy(array $criteria, array $orderBy = null)
 * @method StoryboardSection[]    findAll()
 * @method StoryboardSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryboardSectionRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByContentNode;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, StoryboardSection::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin("{$rootAlias}.storyboard", 'contentNode');

        $this->filterByContentNode($queryBuilder, $user, 'contentNode');
    }
}
