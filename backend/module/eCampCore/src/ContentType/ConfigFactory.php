<?php

namespace eCamp\Core\ContentType;

class ConfigFactory {
  
    /**
     * params String $name Content type name is PascalCase
     */
    public static function createConfig(String $name, ?String $namePlural = null) {

        // used in class namespace (PascalCase)
        $namespace = $name;

        // name of entity
        $entity = $namespace;

        // used in folder structure (Prefix + PascalCase)
        $folder = "eCamp".$name;

        // property prefix (camelCase)
        $propertyPrefix = lcfirst($name);

        // route name
        $route = strtolower($name);

        // URI (lower case) + plural
        $apiEndpoint = ! is_null($namePlural) ? strtolower($namePlural) : strtolower($name)."s";


        $config =  [
            'router' => [
                'routes' => [
                    "e-camp-api.rest.doctrine.activity-content.{$route}" => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => "/api/content-type/{$apiEndpoint}[/:{$propertyPrefix}Id]",
                            'defaults' => [
                                'controller' => "eCamp\\ContentType\\{$namespace}\\Controller\\{$entity}Controller",
                            ],
                        ],
                    ],
                ],
            ],
        
            'api-tools-rest' => [
                "eCamp\\ContentType\\{$namespace}\\Controller\\{$entity}Controller" => [
                    'listener' => "eCamp\\ContentType\\{$namespace}\\Service\\{$entity}Service",
                    'controller_class' => "eCamp\\ContentType\\{$namespace}\\Controller\\{$entity}Controller",
                    'route_name' => "e-camp-api.rest.doctrine.activity-content.${route}",
                    'route_identifier_name' => "{$propertyPrefix}Id",
                    'entity_identifier_name' => 'id',
                    'collection_name' => 'items',
                    'entity_http_methods' => [
                        0 => 'GET',
                        1 => 'PATCH',
                        2 => 'PUT',
                        // 3 => 'DELETE' // disallow deleting directly. Single entities should always be deleted via ActivityContent.
                    ],
                    'collection_http_methods' => [
                        0 => 'GET',
                        // 1 => 'POST', // disallow posting directly. Single entities should always be created via ActivityContent.
                    ],
                    'collection_query_whitelist' => [
                        1 => 'page_size',
                    ],
                    'page_size' => -1,
                    'page_size_param' => 'page_size',
                    'entity_class' => "eCamp\\ContentType\\{$namespace}\\Entity\\{$entity}",
                    'collection_class' => "eCamp\\ContentType\\{$namespace}\\Entity\\{$entity}Collection",
                    'service_name' => $entity,
                ],
            ],
        
            'api-tools-hal' => [
                'metadata_map' => [
                    "eCamp\\ContentType\\{$namespace}\\Entity\\{$entity}" => [
                        'route_identifier_name' => "{$propertyPrefix}Id",
                        'entity_identifier_name' => 'id',
                        'route_name' => "e-camp-api.rest.doctrine.activity-content.{$route}",
                        'route_params' => [
                            'activityContentId' => function ($object) {
                                return $object->getActivityContent()->getId();
                            },
                        ],
                        'hydrator' => "eCamp\\ContentType\\{$namespace}\\Hydrator\\{$entity}Hydrator",
                        'max_depth' => 2,
                    ],
                    "eCamp\\ContentType\\{$namespace}\\Entity\\{$entity}Collection" => [
                        'entity_identifier_name' => 'id',
                        'route_name' => "e-camp-api.rest.doctrine.activity-content.{$route}",
                        'is_collection' => true,
                        'max_depth' => 0,
                    ],
                ],
            ],
        
            'doctrine' => [
                'driver' => [
                    "ecamp_contenttype_{$route}_entities" => [
                        'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                        'cache' => 'array',
                        'paths' => [__DIR__."/../../../../content-type/{$folder}/src/Entity"],
                    ],
        
                    'orm_default' => [
                        'drivers' => [
                            "eCamp\\ContentType\\{$namespace}\\Entity" => "ecamp_contenttype_{$route}_entities",
                        ],
                    ],
                ],
            ],
        ];

        return $config;

    }
}