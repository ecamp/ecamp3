<?php

namespace EcampStoryboard\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SectionServiceFactory
    implements FactoryInterface
{
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $sectionRepo = $services->get('EcampStoryboard\Repository\Section');

        return new SectionService($sectionRepo);
    }
}
