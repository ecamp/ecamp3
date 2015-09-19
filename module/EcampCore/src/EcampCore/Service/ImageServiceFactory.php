<?php

namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ImageServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        return new ImageService(
            $services->get('EcampCore\Repository\Image')
        );
    }

}
