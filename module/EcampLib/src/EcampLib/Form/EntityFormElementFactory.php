<?php

namespace EcampLib\Form;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityFormElementFactory implements AbstractFactoryInterface
{
    const EntityManager = 'entitymanager';
    const Pattern = 'pattern';
    const Entity = 'entity';
    const Property = 'property';
    const AnnotationBuilder = 'annotationbuilder';
    const Form = 'form';

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

        foreach ($this->moduleOptions->getEntityFormElementMappings() as $entityPattern => $entityFormElementMapping){
            if(preg_match($entityPattern, $requestedName)){
                $canCreateService = true;

                $this->cache[$requestedName] = array(
                    self::EntityManager => $entityFormElementMapping[self::EntityManager],
                    self::Pattern => $entityPattern,
                    self::Entity => $entityFormElementMapping[self::Entity],
                    self::Property => $entityFormElementMapping[self::Property],
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
        $pattern = $cacheEntry[self::Pattern];
        $entityName = preg_replace($pattern, $cacheEntry[self::Entity], $requestedName);
        $propertyName = preg_replace($pattern, $cacheEntry[self::Property], $requestedName);

        if($cacheEntry[self::AnnotationBuilder] == null){
            $entityManagerName = $cacheEntry[self::EntityManager];

            /** @var AbstractPluginManager $formElementManager */
            $serviceLocator = $formElementManager->getServiceLocator();

            /** @var EntityManager $entityManager */
            $entityManager = $serviceLocator->get('doctrine.entitymanager.' . $entityManagerName);

            $cacheEntry[self::AnnotationBuilder] = new AnnotationBuilder($entityManager);
        }

        if($cacheEntry[self::Form] ==  null){
            /** @var AnnotationBuilder $annotationBuilder */
            $annotationBuilder = $cacheEntry[self::AnnotationBuilder];
            $cacheEntry[self::Form] = $annotationBuilder->createForm($entityName);
        }

        /** @var Form $form */
        $form = $cacheEntry[self::Form];

        return $form->get($propertyName);
    }

}
