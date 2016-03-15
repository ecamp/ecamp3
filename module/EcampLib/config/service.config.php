<?php

return array(

    'aliases' => array(
        'PrintableManager'  => 'EcampLib\ServiceManager\PrintableManager',
        'JobFactoryManager' => 'EcampLib\ServiceManager\JobFactoryManager',
    ),

    'abstract_factories' => array(
        /**
         * Provides repositories for all doctrine entities
         * Pattern: Ecamp*\Repository\*
         */
        'EcampLib\Repository\AbstractRepositoryFactory',

        /**
         * Provides wrapped services for all internal service classes
         * Pattern: Ecamp*\Service\*
         */
        'EcampLib\Service\AbstractServiceFactory',

        /**
         * Provides Phly-Restfully Resources
         * Pattern: Ecamp*\Resource\*
         */
        'EcampLib\Resource\AbstractResourceFactory'

    ),

    'factories' => array(
        'Logger'                                    => 'EcampLib\Log\LoggerFactory',
        'Router'                                    => 'EcampLib\Router\RouterFactory',
        'EcampLib\ServiceManager\PrintableManager'  => 'EcampLib\ServiceManager\PrintableManagerFactory',
        'EcampLib\ServiceManager\JobFactoryManager' => 'EcampLib\ServiceManager\JobFactoryManagerFactory',
    ),

);
