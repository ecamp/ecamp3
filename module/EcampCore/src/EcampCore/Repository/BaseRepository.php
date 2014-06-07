<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

class BaseRepository extends EntityRepository
{
    protected function getAuthenticatedUserId()
    {
        $authService = new \Zend\Authentication\AuthenticationService();
        if ($authService->hasIdentity()) {
            return $authService->getIdentity();
        }

        return null;
    }

    protected function getAuthenticatedUser()
    {
        $authenticatedUserId = $this->getAuthenticatedUserId();
        if ($authenticatedUserId != null) {
            return $this->_em->find('EcampCore\Entity\User', $authenticatedUserId);
        }

        return null;
    }

}
