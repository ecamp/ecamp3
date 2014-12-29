<?php

namespace EcampCore\Plugin;

use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractStrategyFactory
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var EntityManagerInterface
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
     * @return \EcampCore\Plugin\AbstractStrategy
     */
    abstract public function createStrategy();

}
