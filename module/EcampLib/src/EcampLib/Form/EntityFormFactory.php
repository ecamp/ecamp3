<?php

namespace EcampLib\Form;

use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityFormFactory implements AbstractFactoryInterface
{
    private $pattern = "/^Ecamp(\w+)\\\\Entity\\\\(\w+)$/";

    private $orm;

    /**
     * @var AnnotationBuilder
     */
    private $annotationBuilder;

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

        $form = $this->annotationBuilder->createForm($entityName);

        return $form;
    }

}
