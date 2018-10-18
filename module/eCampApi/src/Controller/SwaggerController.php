<?php

namespace eCamp\Api\Controller;

use Symfony\Component\Yaml\Yaml;
use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\DocBlock\Tag\GenericTag;
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
            $name = $zfRestConfig['service_name'];
            $listener = $zfRestConfig['listener'];
            $routeName = $zfRestConfig['route_name'];
            $routeIdentifierName = $zfRestConfig['route_identifier_name'];

            $listenerReflection = new ClassReflection($listener);

            // TODO: Nested Routes
            if (strpos($routeName, '/')) {
                continue;
            }

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

                $funcName = $this->getListenerFunction($method, true);

                $docu['paths'][$collectionPath][strtolower($method)] = [
                    'summary' => $this->getSummary($name, $listener, $funcName),
                    'description' => $this->getDescription($name, $listener, $funcName),
                    'parameters' => $this->getParams($listener, $funcName),
                    'consumes' => [ 'application/json' ],
                    'produces' => [ 'application/json' ],
                    'responses' => [ '200' => ['description' => 'ok'] ]
                ];
            }

            foreach ($entityHttpMethods as $method) {
                if (!array_key_exists($entityPath, $docu['paths'])) {
                    $docu['paths'][$entityPath] = [];
                }

                $funcName = $this->getListenerFunction($method, false);

                $docu['paths'][$entityPath][strtolower($method)] = [
                    'summary' => $this->getSummary($name, $listener, $funcName),
                    'description' => $this->getDescription($name, $listener, $funcName),
                    'parameters' => $this->getParams($listener, $funcName),
                    'consumes' => [ 'application/json' ],
                    'produces' => [ 'application/json' ],
                    'responses' => [ '200' => ['description' => 'ok'] ]
                ];
            }
        }

        header('Access-Control-Allow-Origin: *');

        echo Yaml::dump($docu, 10, 2);
        die();
    }

    protected function getListenerFunction($method, $isCollection) {
        if ($isCollection) {
            switch ($method) {
                case 'GET':
                    return 'fetchAll';
                case 'POST':
                    return 'create';
            }
        } else {
            switch ($method) {
                case 'GET':
                    return 'fetch';
                case 'PATCH':
                    return 'patch';
                case 'DELETE':
                    return 'delete';
            }
        }
        return null;
    }


    protected function getSummary($name, $listener, $funcName) {
        $listenerReflection = new ClassReflection($listener);
        $summary = false;

        try {
            $funcRefl = $listenerReflection->getMethod($funcName);
            $docBlock = $funcRefl->getDocBlock();
            if ($docBlock) {
                $summary = $docBlock->getTag('swaggerTitle');
                if ($summary) {
                    /** @var GenericTag $summary */
                    $summary = $summary->getContent();
                }
            }
        } catch (\Exception $e) {
        }

        if (!$summary) {
            $summary = ucfirst($funcName) . ' ' . $name;
        }
        return $summary;
    }

    protected function getDescription($name, $listener, $funcName) {
        $listenerReflection = new ClassReflection($listener);
        $desc = false;

        try {
            $funcRefl = $listenerReflection->getMethod($funcName);
            $docBlock = $funcRefl->getDocBlock();

            if ($docBlock) {
                $params = $docBlock->getTags('data');
                // var_dump($params);

                $desc = $docBlock->getTag('desc');
                if ($desc) {
                    /** @var GenericTag $desc */
                    $desc = $desc->getContent();
                } else {
                    $desc = $funcRefl->getDocComment();

                    $lines = explode("\n", $desc);

                    $desc = "";
                    foreach ($lines as $key => $line) {
                        $line = trim($line);
                        if ($line == '/**') {
                            continue;
                        }
                        if ($line == '/*') {
                            continue;
                        }
                        if ($line == '*') {
                            continue;
                        }
                        if ($line == '*/') {
                            continue;
                        }
                        if (substr($line, 0, 3) == '* @') {
                            continue;
                        }

                        if (substr($line, 0, 1) == '*') {
                            $line = trim(substr($line, 1));
                        }

                        $line = str_replace("\r", "&emsp;", $line);

                        $desc = $desc . $line . "\r\n";
                    }
                }
            }
        } catch (\Exception $e) {
        }

        if (!$desc) {
            $desc = ucfirst($funcName) . ' ' . $name;
        }
        return $desc;
    }

    protected function getParams($listener, $funcName) {
        $listenerReflection = new ClassReflection($listener);
        $params = [];

        try {
            $funcRefl = $listenerReflection->getMethod($funcName);
            $docBlock = $funcRefl->getDocBlock();

            if ($docBlock) {
                $pathParams = $docBlock->getTags('swaggerPath');

                foreach ($pathParams as $pathParam) {
                    /** @var GenericTag $pathParam */
                    $info = explode(' ', $pathParam->getContent());
                    $type = array_shift($info);
                    $name = array_shift($info);
                    $desc = implode(' ', $info);

                    $params[] = [
                        'in' => 'path',
                        'name' => $name,
                        'type' => $type,
                        'description' => $desc
                    ];
                }

                $queryParams = $docBlock->getTags('swaggerQuery');
                foreach ($queryParams as $queryParam) {
                    /** @var GenericTag $queryParam */
                    $info = explode(' ', $queryParam->getContent());
                    $type = array_shift($info);
                    $name = array_shift($info);
                    $desc = implode(' ', $info);

                    $params[] = [
                        'in' => 'query',
                        'name' => $name,
                        'type' => $type,
                        'description' => $desc
                    ];
                }

                $dataParams = $docBlock->getTags('swaggerBody');
                foreach ($dataParams as $dataParam) {
                    /** @var GenericTag $dataParam */
                    $info = explode(' ', $dataParam->getContent());
                    $type = array_shift($info);
                    $name = array_shift($info);
                    $desc = implode(' ', $info);

                    $params[] = [
                        'in' => 'body',
                        'name' => $name,
                        'type' => $type,
                        'description' => $desc
                    ];
                }
            }
        } catch (\Exception $e) {
        }

        return $params;
    }
}
