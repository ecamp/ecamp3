<?php

namespace eCamp\Api\View;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\Hal\View\HalJsonModel;

class AcaoInjector extends AbstractListenerAggregate {
    public function attach(EventManagerInterface $events, $priority = 1) {
        $this->listeners = $events->attach(MvcEvent::EVENT_FINISH, [$this, 'onFinish'], -9999);
    }

    public function onFinish(MvcEvent $e) {
        $allowAllOrigin = false;
        $req = $e->getRequest();
        $res = $e->getResult();
        $resp = $e->getResponse();

        if ($resp instanceof Response && $req instanceof Request) {
            $allowAllOrigin = ($req->getMethod() == Request::METHOD_OPTIONS);
        }
        if ($res instanceof HalJsonModel) {
            $allowAllOrigin = true;
        }
        if ($resp instanceof ApiProblemResponse) {
            $allowAllOrigin = true;
        }
        if ($resp instanceof Response && $resp->getStatusCode() == 405) {
            $allowAllOrigin = true;
        }

        if ($allowAllOrigin) {
            $headers = $resp->getHeaders();
            $headers->addHeaderLine('Access-Control-Allow-Origin', '*');
        }
    }
}