<?php

namespace EcampLib\Form;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityFormFactory implements AbstractFactoryInterface
{

    const EntityManager = 'entitymanager';
    const AnnotationBuilder = 'annotationbuilder';

    private $cache = array();

    /** @var \EcampLib\Options\ModuleOptions */
    private $moduleOptions = null;


    public function canCreateServiceWithName(ServiceLocatorInterface $formElementManager, $name, $requestedName)
    {
        if($this->moduleOptions == null){
            /** @var AbstractPluginManager $formElementManager */
            $serviceLocator = $formElementManager->getServiceLocator();
            $this->moduleOptions = $serviceLocator->get('EcampLib\Options\ModuleOptions');
        }

        $canCreateService = false;

        foreach ($this->moduleOptions->getEntityFormMappings() as $entityPattern => $entityFormMapping) {
            if(preg_match($entityPattern, $requestedName)){
                $canCreateService = true;

                $this->cache[$requestedName] = array(
                    self::EntityManager => $entityFormMapping[self::EntityManager]
                );

                break;
            }
        }

        return $canCreateService;
    }

    public function createServiceWithName(ServiceLocatorInterface $formElementManager, $name, $requestedName)
    {
        if(!$this->canCreateServiceWithName($formElementManager, $name, $requestedName)){
            return null;
        }

        $cacheEntry = $this->cache[$requestedName];

        if($cacheEntry[self::AnnotationBuilder] == null){
            $entityManagerName = $cacheEntry[self::EntityManager];

            /** @var AbstractPluginManager $formElementManager */
            $serviceLocator = $formElementManager->getServiceLocator();

            /** @var EntityManager $entityManager */
            $entityManager = $serviceLocator->get('doctrine.entitymanager.' . $entityManagerName);

            $cacheEntry[self::AnnotationBuilder] = new AnnotationBuilder($entityManager);
        }

        /** @var AnnotationBuilder $annotationBuilder */
        $annotationBuilder = $cacheEntry[self::AnnotationBuilder];
        return $annotationBuilder->createForm($requestedName);
    }

}
