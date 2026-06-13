<?php

declare(strict_types=1);

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\UserCamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Comment find($id, $lockMode = null, $lockVersion = null)
 * @method null|Comment findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Comment::class);
    }

    #[\Override]
    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $campsQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        $campsQry->select('identity(uc.camp)');
        $campsQry->from(UserCamp::class, 'uc');
        $campsQry->where('uc.user = :current_user');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                "{$rootAlias}.author = :current_user",
                $queryBuilder->expr()->in("{$rootAlias}.camp", $campsQry->getDQL())
            )
        );
        $queryBuilder->setParameter('current_user', $user);
    }
}
