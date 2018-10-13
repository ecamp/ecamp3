<?php

namespace eCamp\Api\Controller;

use Symfony\Component\Yaml\Yaml;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Router\Http\TreeRouteStack;

class SwaggerController extends AbstractActionController {
    private $config;

    /**
     * @var TreeRouteStack
     */
    private $router;


    public function __construct(array $config, $router) {
        $this->config = $config;
        $this->router = $router;
    }

    public function indexAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $url = $request->getUri();

        $def = $url->getScheme() . '://' . $url->getHost() . ':' . $url->getPort();
        $def = $def . $this->url()->fromRoute('ecamp.api-docu', ['action' => 'swagger']);

        $swagger = $url->getScheme() . '://petstore.swagger.io?url=';

        $this->redirect()->toUrl($swagger . $def);
    }

    public function swaggerAction() {
        $zfRestConfigs = $this->config['zf-rest'];

        /** @var Request $request */
        $request = $this->getRequest();
        $uri = $request->getUri();
        $host = $uri->getHost() . ':' . $uri->getPort();
        $basePath = $this->url()->fromRoute('ecamp.api');
        $lenBasePath = strlen($basePath);

        $docu = [
            'swagger' => '2.0',
            'info' => [
                'title' => 'eCampApi',
                'version' => '1.0.0',
            ],
            'host' => $host,
            'basePath' => $basePath,
            'schemes' => ['http'],
            'paths' => []
        ];


        //var_dump($zfRestConfigs);

        foreach ($zfRestConfigs as $zfRestConfig) {
            $routeName = $zfRestConfig['route_name'];
            $routeIdentifierName = $zfRestConfig['route_identifier_name'];

            // TODO: Nested Routes
            if (strpos($routeName, '/')) { continue; }

//             $routeElements = explode('/', $routeName);
//            /** @var Part $route */
//            $route = $this->router; // ->getRoute($routeName);
//
//            foreach($routeElements as $routeElement) {
//                $route = $route->getRoute($routeElement);
//                var_dump($routeElement, $route);
//            }

            $collectionPath = $this->url()->fromRoute($routeName);

            if (substr($collectionPath, 0, $lenBasePath) == $basePath) {
                $collectionPath = substr($collectionPath, $lenBasePath);
            }
            $collectionHttpMethods = $zfRestConfig['collection_http_methods'];

            $entityPath = $this->url()->fromRoute($routeName, [$routeIdentifierName => '___id___']);
            $entityPath = str_replace('___id___', '{id}', $entityPath);
            if (substr($entityPath, 0, $lenBasePath) == $basePath) {
                $entityPath = substr($entityPath, $lenBasePath);
            }
            $entityHttpMethods = $zfRestConfig['entity_http_methods'];


            foreach ($collectionHttpMethods as $method) {
                if (!array_key_exists($collectionPath, $docu['paths'])) {
                    $docu['paths'][$collectionPath] = [];
                }

                $docu['paths'][$collectionPath][strtolower($method)] = [
                    'consumes' => [ 'application/json' ],
                    'produces' => [ 'application/json' ],
                    'responses' => [ '200' => ['description' => 'ok'] ]
                ];
            }

            foreach ($entityHttpMethods as $method) {
                if (!array_key_exists($entityPath, $docu['paths'])) {
                    $docu['paths'][$entityPath] = [];
                }

                $docu['paths'][$entityPath][strtolower($method)] = [
                    'consumes' => [ 'application/json' ],
                    'produces' => [ 'application/json' ],
                    'parameters' => [
                        [
                            "name" => "id",
                            "in" => "path",
                            "required" => true,
                            "type" => "string"
                        ]
                    ],
                    'responses' => [ '200' => ['description' => 'ok'] ]
                ];
            }
        }

        header('Access-Control-Allow-Origin: *');

        echo Yaml::dump($docu, 10, 2);
        die();
    }
}
