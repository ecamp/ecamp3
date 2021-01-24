<?php

namespace eCamp\Core\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use eCamp\Core\Entity\User;

class UserRepository extends EntityRepository {
    /**
     * @param $username
     */
    public function findByUsername($username): ?User {
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
     */
    public function findByTrustedMail($mail): ?User {
        $q = $this->createQueryBuilder('u');
        $q->join('u.trustedMailAddress', 'tm');
        $q->where('tm.mail = :mail');
        $q->setParameter('mail', $mail);

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
     */
    public function findByUntrustedMail($mail): ?User {
        $q = $this->createQueryBuilder('u');
        $q->join('u.untrustedMailAddress', 'utm');
        $q->where('utm.mail = :mail');
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
