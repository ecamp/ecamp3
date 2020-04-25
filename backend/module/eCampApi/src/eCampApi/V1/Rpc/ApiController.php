<?php

namespace eCampApi\V1\Rpc;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\Hal\Entity;
use ZF\Hal\Plugin\Hal;
use ZF\Hal\View\HalJsonModel;

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
        /** @var Hal $plugin */
        $plugin = $this->plugin('Hal');
        return $plugin->createEntity($entity, $route, $routeIdentifierName);
    }
}
