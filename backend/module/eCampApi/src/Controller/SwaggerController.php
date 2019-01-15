<?php

namespace eCamp\Api\Controller;

use Symfony\Component\Yaml\Yaml;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class SwaggerController extends AbstractActionController {
    private $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function indexAction() {
        $viewModel = new JsonModel();

        $zfRestConfigs = $this->config['zf-rest'];

        /** @var Request $request */
        $request = $this->getRequest();
        $host = $request->getUri()->getHost();
        $basePath = $this->url()->fromRoute('ecamp.api');
        $lenBasePath = strlen($basePath);

        $docu = [
            'swagger' => '2.0',
            'host' => $host,
            'basePath' => $basePath,
            'paths' => []
        ];

        foreach ($zfRestConfigs as $zfRestConfig) {
            $routeName = $zfRestConfig['route_name'];
            $routeIdentifierName = $zfRestConfig['route_identifier_name'];

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

                $docu['paths'][$collectionPath][$method] = [
                    'consumes' => [ 'application/json' ],
                    'produces' => [ 'application/json' ],
                ];
            }

            foreach ($entityHttpMethods as $method) {
                if (!array_key_exists($entityPath, $docu['paths'])) {
                    $docu['paths'][$entityPath] = [];
                }

                $docu['paths'][$entityPath][$method] = [
                    'consumes' => [ 'application/json' ],
                    'produces' => [ 'application/json' ],
                ];
            }
        }

        echo Yaml::dump($docu, 10, 2);
        die();
    }
}
