<?php

namespace eCampApi;

class ConfigFactory {
    /**
     * params String $name         Content type name is PascalCase
     * params String $namePlural   Specify non-standard plural names.
     * params String $entityName   Specify entity name if it deviates from main name.
     */
    public static function createConfig(string $name, ?string $namePlural = null) {
        // route name
        $route = 'e-camp-api.rest.doctrine.'.self::pascalToKebabCase($name);

        // URI (kebab case) + plural
        $apiEndpoint = !is_null($namePlural) ? $namePlural : $name.'s';
        $apiEndpoint = self::pascalToKebabCase($apiEndpoint);

        // name of entity
        $entity = $name;

        // property prefix (camelCase)
        $propertyPrefix = lcfirst($entity);

        return [
            'router' => [
                'routes' => [
                    $route => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => "/api/{$apiEndpoint}[/:{$propertyPrefix}Id]",
                            'defaults' => [
                                'controller' => "eCampApi\\V1\\Rest\\{$entity}\\Controller",
                            ],
                        ],
                    ],
                ],
            ],
            'api-tools-rest' => [
                "eCampApi\\V1\\Rest\\{$entity}\\Controller" => [
                    'listener' => "eCamp\\Core\\EntityService\\{$entity}Service",
                    'route_name' => $route,
                    'route_identifier_name' => "{$propertyPrefix}Id",
                    'entity_identifier_name' => 'id',
                    'collection_name' => 'items',
                    'entity_http_methods' => [
                        0 => 'GET',
                        1 => 'PATCH',
                        2 => 'PUT',
                        3 => 'DELETE',
                    ],
                    'collection_http_methods' => [
                        0 => 'GET',
                        1 => 'POST',
                    ],
                    'collection_query_whitelist' => [
                        0 => 'page_size',
                    ],
                    'page_size' => -1,
                    'page_size_param' => 'page_size',
                    'entity_class' => "eCamp\\Core\\Entity\\{$entity}",
                    'collection_class' => "eCampApi\\V1\\Rest\\{$entity}\\{$entity}Collection",
                    'service_name' => $entity,
                ],
            ],
            'api-tools-content-negotiation' => [
                'controllers' => [
                    "eCampApi\\V1\\Rest\\{$entity}\\Controller" => 'HalJson',
                ],
                'accept_whitelist' => [
                    "eCampApi\\V1\\Rest\\{$entity}\\Controller" => [
                        0 => 'application/vnd.e-camp-api.v1+json',
                        1 => 'application/hal+json',
                        2 => 'application/json',
                    ],
                ],
                'content_type_whitelist' => [
                    "eCampApi\\V1\\Rest\\{$entity}\\Controller" => [
                        0 => 'application/vnd.e-camp-api.v1+json',
                        1 => 'application/json',
                    ],
                ],
            ],
            'api-tools-hal' => [
                'metadata_map' => [
                    "eCamp\\Core\\Entity\\{$entity}" => [
                        'route_identifier_name' => "{$propertyPrefix}Id",
                        'entity_identifier_name' => 'id',
                        'route_name' => $route,
                        'hydrator' => "eCamp\\Core\\Hydrator\\{$entity}Hydrator",
                        'max_depth' => 20,
                    ],
                    "eCampApi\\V1\\Rest\\{$entity}\\{$entity}Collection" => [
                        'entity_identifier_name' => 'id',
                        'route_name' => $route,
                        'is_collection' => true,
                        'max_depth' => 20,
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns Pascal in kebab-case.
     *
     * @param mixed $pascal
     */
    private static function pascalToKebabCase($pascal) {
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', $pascal)), '-');
    }
}
