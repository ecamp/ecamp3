<?php

namespace App\Repository;

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

    public function filterByUser(QueryBuilder $queryBuilder, User $user): void {
        $userAlias = self::getOrCreateUserAlias($queryBuilder);
        $userCampCollaborationsAlias = self::getOrCreateCampCollaborationAlias($queryBuilder, $userAlias);
        $queryBuilder->leftJoin("{$userCampCollaborationsAlias}.camp", 'camp');
        $queryBuilder->leftJoin('camp.collaborations', 'relatedCampCollaborations');
        $expr = new Expr();
        $queryBuilder->andWhere($expr->orX(
            $expr->eq($userAlias, ':current_user'),
            $expr->andX(
                $expr->eq("{$userCampCollaborationsAlias}.status", ':status_established'),
                $expr->eq('relatedCampCollaborations.status', ':status_established'),
                $expr->eq('relatedCampCollaborations.user', ' :current_user'),
            )
        ));
        $queryBuilder->setParameter('current_user', $user);
        $queryBuilder->setParameter('status_established', CampCollaboration::STATUS_ESTABLISHED);
    }

    private static function getOrCreateUserAlias(QueryBuilder $queryBuilder) {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        foreach ($queryBuilder->getDQLPart('join') as $rootAliasJoins) {
            foreach ($rootAliasJoins as $joinPart) {
                /** @var Expr\Join $joinPart */
                if (str_contains($joinPart->getJoin(), '.user')) {
                    return $joinPart->getAlias();
                }
            }
        }
        $userAlias = 'user';
        $queryBuilder->join("{$rootAlias}.user", $userAlias);

        return $userAlias;
    }

    private static function getOrCreateCampCollaborationAlias(QueryBuilder $queryBuilder, string $userAlias) {
        foreach ($queryBuilder->getDQLPart('join') as $rootAliasJoins) {
            foreach ($rootAliasJoins as $joinPart) {
                /** @var Expr\Join $joinPart */
                if (str_contains($joinPart->getJoin(), '.collaborations')) {
                    return $joinPart->getAlias();
                }
            }
        }
        $collaborationsAlias = 'userCampCollaborations';
        $queryBuilder->leftJoin("{$userAlias}.collaborations", $collaborationsAlias);

        return $collaborationsAlias;
    }
}
