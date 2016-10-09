<?php

namespace EcampCore\Job\Mail;

use EcampCore\Repository\UserRepository;
use EcampLib\Job\JobFactoryInterface;
use EcampLib\Job\JobInterface;
use EcampLib\ServiceManager\JobFactoryManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class SendEmailVerificationEmailJobFactory implements JobFactoryInterface
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $jobFactoryManager
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $jobFactoryManager)
    {
        /** @var JobFactoryManager $jobFactoryManager */
        $serviceLocator = $jobFactoryManager->getServiceLocator();
        
        $this->userRepository = $serviceLocator->get('EcampCore\Repository\User');

        return $this;
    }
    

    /**
     * @param  array $options
     * @return JobInterface
     */
    public function create($options = null)
    {
        $userId = $options['userId'];
        /** @var \EcampCore\Entity\User $user */
        $user = $this->userRepository->find($userId);

        return new SendEmailVerificationEmailJob($user);
    }
}
