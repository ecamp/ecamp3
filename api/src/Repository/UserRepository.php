<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method null|User find($id, $lockMode = null, $lockVersion = null)
 * @method null|User findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->password = $newHashedPassword;
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function loadUserByIdentifier(string $identifier): ?User {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('user');
        $queryBuilder->from(User::class, 'user');
        $queryBuilder->join('user.profile', 'profile');
        $queryBuilder->orWhere('profile.email = :identifier');
        $queryBuilder->setParameter('identifier', $identifier);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
