<?php

namespace eCampApi\V1\Rpc;

use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Plugin\Hal;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;

class ApiController extends AbstractActionController {
    public function onDispatch(MvcEvent $e) {
        try {
            $return = parent::onDispatch($e);
        } catch (\Exception $ex) {
            $return = new ApiProblem(500, $ex->getMessage());
        }

        if ($return instanceof ApiProblem) {
            $problem = new ApiProblemResponse($return);

            $e->setResult($problem);

            return $problem;
        }

        if ($return instanceof Entity) {
            $json = new HalJsonModel();
            $json->setPayload($return);

            $e->setResult($json);

            return $json;
        }

        return $return;
    }

    protected function createHalEntity($entity, $route, $routeIdentifierName) {
        /** @var Hal $contentType */
        $plugin = $this->plugin('Hal');

        return $plugin->createEntity($entity, $route, $routeIdentifierName);
    }
}
