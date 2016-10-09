<?php

namespace EcampCore\Job\Mail;

use EcampCore\Repository\LoginRepository;
use EcampLib\Job\JobFactoryInterface;
use EcampLib\Job\JobInterface;
use EcampLib\ServiceManager\JobFactoryManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class SendPwResetMailJobFactory implements JobFactoryInterface
{
    /** @var LoginRepository */
    private $loginRepository;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $jobFactoryManager
     * @return SendPwResetMailJobFactory
     */
    public function createService(ServiceLocatorInterface $jobFactoryManager)
    {
        /** @var JobFactoryManager $jobFactoryManager */
        $serviceLocator = $jobFactoryManager->getServiceLocator();

        $this->loginRepository = $serviceLocator->get('EcampCore\Repository\Login');

        return $this;
    }

    /**
     * @param  array $options
     * @return JobInterface
     */
    public function create($options = null)
    {
        $loginId = $options['loginId'];
        /** @var \EcampCore\Entity\Login $login */
        $login = $this->loginRepository->find($loginId);
        
        return new SendPwResetMailJob($login);
    }
}
