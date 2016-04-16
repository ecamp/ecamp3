<?php

namespace EcampCore\Service\Factory;

use EcampCore\Service\ImageService;
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
