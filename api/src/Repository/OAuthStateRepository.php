<?php

namespace App\Repository;

use App\Entity\OAuthState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|OAuthState find($id, $lockMode = null, $lockVersion = null)
 * @method null|OAuthState findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthState[]    findAll()
 * @method OAuthState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthStateRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, OAuthState::class);
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneUnexpiredBy(string $state, \DateTime $expiresAfter): ?OAuthState {
        return $this->createQueryBuilder('o')
            ->where('o.expireTime > :now AND o.state = :state')
            ->setParameter('now', $expiresAfter)
            ->setParameter('state', $state)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    public function deleteAllExpiredBefore(\DateTime $expiry) {
        $this->createQueryBuilder('o')
            ->delete(OAuthState::class, 'o')
            ->where('o.expireTime < :expiry')
            ->setParameter('expiry', $expiry)
            ->getQuery()
            ->execute()
        ;
    }
}
