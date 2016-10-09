<?php

return array(

    'aliases' => array(
        'EcampLib\Job\JobQueue' => EcampLib\Job\Engine\Memory\JobQueue::class,

        'PrintableManager'      => 'EcampLib\ServiceManager\PrintableManager',
        'JobFactoryManager'     => 'EcampLib\ServiceManager\JobFactoryManager',
    ),

    'invokables' => array(

        'EcampLib\Service\ServiceInitializer'       => 'EcampLib\Service\ServiceInitializer',
    ),

    'factories' => array(
        'Logger'                                    => 'EcampLib\Log\LoggerFactory',
        'EcampLib\Options\ModuleOptions'            => 'EcampLib\Options\ModuleOptionsFactory',
        'EcampLib\ServiceManager\PrintableManager'  => 'EcampLib\ServiceManager\PrintableManagerFactory',
        'EcampLib\ServiceManager\JobFactoryManager' => 'EcampLib\ServiceManager\JobFactoryManagerFactory',

        EcampLib\Job\Engine\Memory\JobQueue::class  => EcampLib\Job\Engine\Memory\JobQueueFactory::class,
        EcampLib\Job\Engine\Resque\JobQueue::class  => \EcampLib\Job\Engine\Resque\JobQueueFactory::class
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
);
