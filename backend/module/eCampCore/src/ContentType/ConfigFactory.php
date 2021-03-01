<?php

namespace eCamp\Core\ContentType;

class ConfigFactory {
    /**
     * @param string      $name       Content type name is PascalCase
     * @param bool        $multiple   false=single entitity per contentNode; true=multiple entities per contentNode
     * @param null|string $entityName Specify entity name if it deviates from main name
     * @param null|string $namePlural specify non-standard plural names
     */
    public static function createConfig(string $name, bool $multiple = false, ?string $entityName = null, ?string $namePlural = null): array {
        // used in class namespace (PascalCase)
        $namespace = $name;

        // used in folder structure (Prefix + PascalCase)
        $folder = 'eCamp'.$name;

        // route name
        $route = strtolower($name);

        // URI (lower case) + plural
        $apiEndpoint = strtolower($namePlural ?? $name.'s');

        // name of entity ($namespace s fallback)
        $entity = $entityName ?? $namespace;

        // property prefix (camelCase)
        $propertyPrefix = lcfirst($entity);

        $config = [
            'router' => [
                'routes' => [
                    "e-camp-api.rest.doctrine.content-node.{$route}" => [
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
                    'route_name' => "e-camp-api.rest.doctrine.content-node.{$route}",
                    'route_identifier_name' => "{$propertyPrefix}Id",
                    'entity_identifier_name' => 'id',
                    'collection_name' => 'items',
                    'entity_http_methods' => [
                        0 => 'GET',
                        1 => 'PATCH',
                        2 => 'PUT',
                        3 => 'DELETE', // TODO: disallow deleting directly. Single entities should always be deleted via ContentNode.
                    ],
                    'collection_http_methods' => [
                        0 => 'GET',
                        1 => 'POST', // TODO: disallow posting directly. Single entities should always be created via ContentNode.
                    ],
                    'collection_query_whitelist' => [
                        0 => 'page_size',
                        1 => 'contentNodeId', // TO DO: not needed for sigle entities
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
                        'route_name' => "e-camp-api.rest.doctrine.content-node.{$route}",
                        'route_params' => [
                            'activityCocontentNodeIdntentId' => function ($object) {
                                return $object->getContentNode()->getId();
                            },
                        ],
                        'hydrator' => "eCamp\\ContentType\\{$namespace}\\Hydrator\\{$entity}Hydrator",
                        'max_depth' => 2,
                    ],
                    "eCamp\\ContentType\\{$namespace}\\Entity\\{$entity}Collection" => [
                        'entity_identifier_name' => 'id',
                        'route_name' => "e-camp-api.rest.doctrine.content-node.{$route}",
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
