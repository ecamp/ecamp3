<?php

namespace EcampCore\Plugin;

use EcampCore\Entity\Medium;
use EcampCore\Entity\EventPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractStrategyFactory
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var unknown
     */
    protected $entityManager;

    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        EntityManagerInterface $entityManager
    ){
        $this->serviceLocator = $serviceLocator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param  EventPlugin                        $eventPlugin
     * @param  Medium                             $medium
     * @return \EcampCore\Plugin\AbstractStrategy
     */
    abstract public function createStrategy();

}
