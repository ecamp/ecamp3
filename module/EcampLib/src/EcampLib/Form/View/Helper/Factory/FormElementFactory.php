<?php

namespace EcampLib\Form\View\Helper\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use EcampLib\Form\View\Helper\FormElement;

class FormElementFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Zend\View\HelperPluginManager $serviceLocator */
        /** @var \TwbBundle\Options\ModuleOptions $options */
        $options = $serviceLocator->getServiceLocator()->get('TwbBundle\Options\ModuleOptions');

        return new FormElement($options);
    }
}
