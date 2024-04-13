<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\CampCollaboration;
use App\Entity\Profile;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Profile find($id, $lockMode = null, $lockVersion = null)
 * @method null|Profile findOneBy(array $criteria, array $orderBy = null)
 * @method Profile[]    findAll()
 * @method Profile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Profile::class);
    }

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $expr = new Expr();

        $relatedUserSubQueryBuilder = $this->getEntityManager()->createQueryBuilder();

        $innerUserAlias = $queryNameGenerator->generateJoinAlias('user');
        $userCollaborationsAlias = $queryNameGenerator->generateJoinAlias('collaborations');
        $campAlias = $queryNameGenerator->generateJoinAlias('camp');
        $relatedCollaborationsAlias = $queryNameGenerator->generateJoinAlias('collaborations');

        $relatedUserQuery = $relatedUserSubQueryBuilder
            ->select($innerUserAlias)
            ->from(User::class, $innerUserAlias)
            ->join("{$innerUserAlias}.collaborations", $userCollaborationsAlias)
            ->join("{$userCollaborationsAlias}.camp", $campAlias)
            ->join("{$campAlias}.collaborations", $relatedCollaborationsAlias)
            ->where(
                $expr->andX(
                    $expr->eq("{$userCollaborationsAlias}.status", ':status_established'),
                    $expr->eq("{$relatedCollaborationsAlias}.status", ':status_established'),
                    $expr->eq("{$relatedCollaborationsAlias}.user", ' :current_user'),
                )
            )
        ;

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $userAlias = $queryNameGenerator->generateJoinAlias('user');
        $queryBuilder->join("{$rootAlias}.user", $userAlias);
        $queryBuilder->andWhere($expr->orX(
            $expr->eq($userAlias, ':current_user'),
            $expr->in(
                $userAlias,
                $relatedUserQuery->getDQL()
            )
        ));
        $queryBuilder->setParameter('current_user', $user);
        $queryBuilder->setParameter('status_established', CampCollaboration::STATUS_ESTABLISHED);
    }
}
