<?php

namespace eCamp\Core\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use eCamp\Core\Entity\User;

class UserRepository extends EntityRepository {
    /**
     * @param $username
     * @return mixed
     */
    public function findByUsername($username) {
        $q = $this->createQueryBuilder('u');
        $q->where('u.username = :username');
        $q->setParameter('username', $username);

        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $mail
     * @return User
     * @throws NonUniqueResultException
     */
    public function findByMail($mail) {
        $q = $this->createQueryBuilder('u');
        $q->leftJoin('u.trustedMailAddress', 'tm');
        $q->leftJoin('u.untrustedMailAddress', 'utm');
        $q->where($q->expr()->orX(
            'tm.mail = :tm_mail',
            'utm.mail = :utm_mail'
        ));

        $q->setParameter('tm_mail', $mail);
        $q->setParameter('utm_mail', $mail);

        return $q->getQuery()->getOneOrNullResult();
    }
}
