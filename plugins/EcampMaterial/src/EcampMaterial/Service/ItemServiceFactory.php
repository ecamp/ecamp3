<?php

namespace EcampMaterial\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ItemServiceFactory
    implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $itemRepo = $services->get('EcampMaterial\Repository\MaterialItem');

        return new ItemService($itemRepo);
    }
}
