<?php

namespace eCampApi\V1\Rpc;

use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Plugin\Hal;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Validator\Exception\InvalidArgumentException;

class ApiController extends AbstractActionController {
    public function onDispatch(MvcEvent $e) {
        try {
            $return = parent::onDispatch($e);
        } catch (InvalidArgumentException $ex) {
            $return = new ApiProblem(400, $ex->getMessage());
        } catch (\Exception $ex) {
            $return = new ApiProblem(500, $ex->getMessage());
        }

        if ($return instanceof ApiProblem) {
            $problem = new ApiProblemResponse($return);

            $e->setResult($problem);

            return $problem;
        }

        return $return;
    }

    protected function entityToHalJsonModel(Entity $entity): HalJsonModel {
        $json = new HalJsonModel();
        $json->setPayload($entity);

        return $json;
    }

    protected function createHalEntity($entity, $route, $routeIdentifierName) {
        /** @var Hal $plugin */
        $plugin = $this->plugin('Hal');

        return $plugin->createEntity($entity, $route, $routeIdentifierName);
    }
}
