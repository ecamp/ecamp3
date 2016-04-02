<?php

namespace EcampLib\Options;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ModuleOptionsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $ecamp = ($config['ecamp'] ?: array());

        $doctrine = ($ecamp['doctrine'] ?: array());
        $repositories = ($doctrine['repository'] ?: array());
        $entityForms = ($doctrine['entity_form'] ?: array());
        $entityFormElements = ($doctrine['entity_form_element'] ?: array());

        $serviceManager = ($ecamp['service_manager'] ?: array());
        $abstractServiceFactoryConfigs = ($serviceManager['abstract_service_factory_config'] ?: array());

        $repositoryMappings = array();
        foreach ($repositories as $repository) {
            $mappings = $repository['mappings'] ?: array();

            foreach ($mappings as $repo => $entityName) {
                $repositoryMappings[$repo] = array(
                    'entitymanager' => $repository['entitymanager'],
                    'entityname' => $entityName,
                );
            }
        }

        $entityFormMappings = array();
        foreach ($entityForms as $entityForm) {
            $pattern = $entityForm['pattern'];
            $entityFormMappings[$pattern] = array(
                'entitymanager' => $entityForm['entitymanager'],
            );
        }

        $entityFormElementMappings = array();
        foreach ($entityFormElements as $entityFormElement) {
            $pattern = $entityFormElement['pattern'];
            $entityFormElementMappings[$pattern] = array(
                'entitymanager' => $entityFormElement['entitymanager'],
                'entity' => $entityFormElement['entity'],
                'property' => $entityFormElement['property'],
            );
        }

        $serviceMappings = array();
        foreach ($abstractServiceFactoryConfigs as $abstractServiceFactoryConfig) {
            $servicePattern = $abstractServiceFactoryConfig['servicePattern'];
            $factoryPattern = $abstractServiceFactoryConfig['factoryPattern'];

            $serviceMappings[$servicePattern] = array(
                'factory' => $factoryPattern
            );
        }

        return new ModuleOptions(array(
            'repositoryMappings' => $repositoryMappings,
            'entityFormMappings' => $entityFormMappings,
            'entityFormElementMappings' => $entityFormElementMappings,
            'serviceMappings' => $serviceMappings,
        ));
    }

}
