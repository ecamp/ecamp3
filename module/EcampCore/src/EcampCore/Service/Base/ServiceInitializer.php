<?php

namespace EcampCore\Service\Base;

use Doctrine\ORM\EntityManager;
use EcampCore\Entity\User;
use EcampCore\Repository\UserRepository;
use EcampLib\Service\ServiceInitializer as LibServiceInitializer;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\Event;

class ServiceInitializer extends LibServiceInitializer
{
    /** @var EntityManager */
    private $entityManager;

    /** @var AuthenticationService */
    private $authService;

    /** @var User */
    private $user;

    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\User');
    }

    public function onCreated(Event $e)
    {
        parent::onCreated($e);

        if ($this->authService == null) {
            $this->authService = new AuthenticationService();
        }

        if ($this->user == null) {
            if ($this->authService->hasIdentity()) {
                $userId = $this->authService->getIdentity();
                $this->user = $this->getUserRepository()->find($userId);
            }

            if ($this->user == null) {
                $this->authService->clearIdentity();
            }
        }

        if ($this->entityManager == null) {
            $this->entityManager = $this->getEntityManager();
        }

        /** @var ServiceBase $service */
        $service = $e->getParam('service');
        $service->setMe($this->user);
        $service->setEntityManager($this->entityManager);
    }
}
