<?php

namespace eCamp\Api\View;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use ZF\Hal\View\HalJsonModel;

class AcaoInjector extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events, $priority = 1) {
        $this->listeners = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], -1000);
    }

    public function onDispatch(MvcEvent $e) {
        $res = $e->getResult();

        if ($res instanceof HalJsonModel) {
            $resp = $e->getResponse();

            if ($resp instanceof Response) {
                $headers = $resp->getHeaders();
                $headers->addHeaderLine('Access-Control-Allow-Origin', '*');
            }
        }
    }
}