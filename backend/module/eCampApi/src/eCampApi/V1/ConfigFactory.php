<?php

namespace eCampApi\V1;

class ConfigFactory {
    private $nameSingular;
    private $namePlural;
    private $routeName;
    private $routeIdentifierName;
    private $endpoint;
    private $entityHttpMethods;
    private $collectionHttpMethods;
    private $collectionQueryWhitelist = ['page_size'];
    private $inputFilterItems = [];

    public function __construct(string $nameSingular) {
        $this->nameSingular = $nameSingular;
    }

    public static function Create(string $nameSingular, ?string $namePlural = null): ConfigFactory {
        $factory = new self($nameSingular);
        if (isset($namePlural)) {
            $factory->setNamePlural($namePlural);
        }

        return $factory;
    }

    public function getNameSingular(): string {
        return $this->nameSingular;
    }

    public function setNameSingular(string $nameSingular): ConfigFactory {
        $this->nameSingular = $nameSingular;

        return $this;
    }

    public function getNamePlural(): string {
        return $this->namePlural ?? ($this->nameSingular.'s');
    }

    public function setNamePlural(string $namePlural): ConfigFactory {
        $this->namePlural = $namePlural;

        return $this;
    }

    public function getRouteName(): string {
        return $this->routeName ?? ('e-camp-api.rest.doctrine.'.self::toKebabCase($this->nameSingular));
    }

    public function setRouteName(string $routeName): ConfigFactory {
        $this->routeName = $routeName;

        return $this;
    }

    public function getRouteIdentifierName(): string {
        return $this->routeIdentifierName ?? (lcfirst($this->nameSingular).'Id');
    }

    public function setRouteIdentifierName(string $routeIdentifierName): ConfigFactory {
        $this->routeIdentifierName = $routeIdentifierName;

        return $this;
    }

    public function getEndpoint(): string {
        return $this->endpoint ?? self::toKebabCase($this->namePlural);
    }

    public function setEndpoint(string $endpoint): ConfigFactory {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getEntityHttpMethods(): array {
        return $this->entityHttpMethods ?? ['GET', 'PATCH', 'DELETE'];
    }

    public function setEntityHttpMethods(array $entityHttpMethods): ConfigFactory {
        $this->entityHttpMethods = $entityHttpMethods;

        return $this;
    }

    public function setEntityHttpMethodsReadonly(): ConfigFactory {
        $this->setEntityHttpMethods(['GET']);

        return $this;
    }

    public function getCollectionHttpMethods(): array {
        return $this->collectionHttpMethods ?? ['GET', 'POST'];
    }

    public function setCollectionHttpMethods(array $collectionHttpMethods): ConfigFactory {
        $this->collectionHttpMethods = $collectionHttpMethods;

        return $this;
    }

    public function setCollectionHttpMethodsReadonly(): ConfigFactory {
        $this->setCollectionHttpMethods(['GET']);

        return $this;
    }

    public function getCollectionQueryWhitelist(): array {
        return $this->collectionQueryWhitelist;
    }

    public function addCollectionQueryWhitelist(...$queries): ConfigFactory {
        array_push($this->collectionQueryWhitelist, ...$queries);

        return $this;
    }

    public function getInputFilterItems(): array {
        return $this->inputFilterItems;
    }

    public function addInputFilterItem(
        string $name,
        bool $required = false,
        ?array $filters = [],
        ?array $validators = []
    ): ConfigFactory {
        $this->inputFilterItems[] = [
            'name' => $name,
            'required' => $required,
            'filters' => $filters,
            'validators' => $validators,
        ];

        return $this;
    }

    public function createInputFilterItem(string $name, bool $required = false): InputFilterFactory {
        return new InputFilterFactory($this, $name, $required);
    }

    public function buildConfig(): array {
        return [
            'router' => $this->buildRouter(),
            'api-tools-rest' => $this->buildApiToolsRest(),
            'api-tools-content-negotiation' => $this->buildApiToolsContentNegotiation(),
            'api-tools-hal' => $this->buildApiToolsHal(),
            'api-tools-content-validation' => $this->buildApiToolsContentValidation(),
            'input_filter_specs' => $this->buildInputFilterSpecs(),
        ];
    }

    private function buildRouter() {
        return [
            'routes' => [
                $this->getRouteName() => [
                    'type' => 'Segment',
                    'options' => [
                        'route' => "/api/{$this->getEndpoint()}[/:{$this->getRouteIdentifierName()}]",
                        'defaults' => [
                            'controller' => $this->getControllerClass(),
                        ],
                    ],
                ],
            ],
        ];
    }

    private function buildApiToolsRest() {
        return [
            $this->getControllerClass() => [
                'listener' => $this->getServiceClass(),
                'route_name' => $this->getRouteName(),
                'route_identifier_name' => $this->getRouteIdentifierName(),
                'entity_identifier_name' => 'id',
                'collection_name' => 'items',
                'entity_http_methods' => $this->getEntityHttpMethods(),
                'collection_http_methods' => $this->getCollectionHttpMethods(),
                'collection_query_whitelist' => $this->getCollectionQueryWhitelist(),
                'page_size' => -1,
                'page_size_param' => 'page_size',
                'entity_class' => $this->getEntityClass(),
                'collection_class' => $this->getCollectionClass(),
                'service_name' => $this->getNameSingular(),
            ],
        ];
    }

    private function buildApiToolsContentNegotiation() {
        return [
            'controllers' => [
                $this->getControllerClass() => 'HalJson',
            ],
            'accept_whitelist' => [
                $this->getControllerClass() => [
                    0 => 'application/vnd.e-camp-api.v1+json',
                    1 => 'application/hal+json',
                    2 => 'application/json',
                ],
            ],
            'content_type_whitelist' => [
                $this->getControllerClass() => [
                    0 => 'application/vnd.e-camp-api.v1+json',
                    1 => 'application/json',
                ],
            ],
        ];
    }

    private function buildApiToolsHal() {
        return [
            'metadata_map' => [
                $this->getEntityClass() => [
                    'route_identifier_name' => $this->getRouteIdentifierName(),
                    'entity_identifier_name' => 'id',
                    'route_name' => $this->getRouteName(),
                    'hydrator' => $this->getHydratorClass(),
                    'max_depth' => 20,
                ],
                $this->getCollectionClass() => [
                    'entity_identifier_name' => 'id',
                    'route_name' => $this->getRouteName(),
                    'is_collection' => true,
                    'max_depth' => 20,
                ],
            ],
        ];
    }

    private function buildApiToolsContentValidation() {
        return [
            $this->getControllerClass() => [
                'input_filter' => $this->getInputFilterClass(),
            ],
        ];
    }

    private function buildInputFilterSpecs() {
        return [
            $this->getInputFilterClass() => $this->getInputFilterItems(),
        ];
    }

    private function getEntityClass() {
        return "eCamp\\Core\\Entity\\{$this->getNameSingular()}";
    }

    private function getCollectionClass() {
        return "eCampApi\\V1\\Rest\\{$this->getNameSingular()}\\{$this->getNameSingular()}Collection";
    }

    private function getControllerClass() {
        return "eCampApi\\V1\\Rest\\{$this->getNameSingular()}\\Controller";
    }

    private function getServiceClass() {
        return "eCamp\\Core\\EntityService\\{$this->getNameSingular()}Service";
    }

    private function getHydratorClass() {
        return "eCamp\\Core\\Hydrator\\{$this->getNameSingular()}Hydrator";
    }

    private function getInputFilterClass() {
        return "eCampApi\\V1\\Rest\\{$this->getNameSingular()}\\Validator";
    }

    private static function toKebabCase($string) {
        return strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', lcfirst($string)));
    }
}
