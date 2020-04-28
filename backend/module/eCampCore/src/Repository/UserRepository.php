<?php

namespace eCamp\Core\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use eCamp\Core\Entity\User;

class UserRepository extends EntityRepository {
    /**
     * @param $username
     *
     * @return mixed
     */
    public function findByUsername($username) {
        $q = $this->createQueryBuilder('u');
        $q->where('u.username = :username');
        $q->setParameter('username', $username);

        try {
            $q->setMaxResults(1);

            return $q->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // This shouldn't ever happen
            return null;
        }
    }

    /**
     * @param string $mail
     *
     * @return User
     */
    public function findByMail($mail) {
        $q = $this->createQueryBuilder('u');
        $q->leftJoin('u.trustedMailAddress', 'tm');
        $q->leftJoin('u.untrustedMailAddress', 'utm');
        $q->where($q->expr()->orX('tm.mail = :mail', 'utm.mail = :mail'));

        $q->setParameter('mail', $mail);

        try {
            $q->setMaxResults(1);

            return $q->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // This shouldn't ever happen
            return null;
        }
    }
}
