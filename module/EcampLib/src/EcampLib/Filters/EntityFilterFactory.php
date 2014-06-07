<?php

namespace EcampLib\Filters;

use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityFilterFactory implements AbstractFactoryInterface
{
    private $pattern = "/^Ecamp(\w+)\\\\Entity\\\\(\w+).(\w+)$/";

    private $orm;

    /**
     * @var AnnotationBuilder
     */
    private $annotationBuilder;

    private $formSpecCache = array();

    public function __construct($orm = null)
    {
        $this->orm = $orm ?: 'doctrine.entitymanager.orm_default';
    }

    /**
     * Translate a repository class name into the corresponding entity class name
     * @param  string $elementName
     * @return string
     */
    private function getEntityClassName($elementName)
    {
        return preg_replace($this->pattern,"Ecamp$1\\\\Entity\\\\$2", $elementName);
    }

    private function getPropertyName($elementName)
    {
        return preg_replace($this->pattern, "$3", $elementName);
    }

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return preg_match($this->pattern, $requestedName) && class_exists($this->getEntityClassName($requestedName));
    }

    public function createServiceWithName(ServiceLocatorInterface $formElementManager, $name, $requestedName)
    {
        $serviceLocator = $formElementManager->getServiceLocator();
        $entityName = $this->getEntityClassName($requestedName);

        if ($this->annotationBuilder == null) {
            $entityManager = $serviceLocator->get($this->orm);
            $this->annotationBuilder = new AnnotationBuilder($entityManager);
        }

        if (array_key_exists($entityName, $this->formSpecCache)) {
            $formSpec = $this->formSpecCache[$entityName];
        } else {
            $formSpec = $this->annotationBuilder->createForm($entityName);
            $this->formSpecCache[$entityName] = $formSpec;
        }

        $elementName = $this->getPropertyName($requestedName);

        return $formSpec->getInputFilter()->get($elementName)->getFilterChain();

    }

}
