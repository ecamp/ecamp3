<?php

return array(

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
        'Router'        => 'EcampLib\Router\RouterFactory',
        'Logger'        => 'EcampLib\Log\LoggerFactory'
    ),

);
