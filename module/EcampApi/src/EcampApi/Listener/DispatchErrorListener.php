<?php

namespace EcampApi\Listener;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Application;
use PhlyRestfully\ApiProblem;

class DispatchErrorListener extends AbstractListenerAggregate
{
    const MODULE_NAMESPACE    = '__NAMESPACE__';

    private $dispatchErrorListener = null;

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -1000);
    }

    public function onRoute(MvcEvent $e)
    {
        if ($this->dispatchErrorListener != null) {
            $e->getApplication()->getEventManager()->detach($this->dispatchErrorListener);
            $this->dispatchErrorListener = null;
        }

        $matches = $e->getRouteMatch();
        if ($matches instanceof RouteMatch) {

            $module = $matches->getParam(self::MODULE_NAMESPACE, false);
            if ($module == 'EcampApi') {
                $this->dispatchErrorListener =
                    $e->getApplication()->getEventManager()
                      ->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 500);
            }
        }
    }

    public function onDispatchError(MvcEvent $e)
    {

        // Do nothing if no error in the event
        $error = $e->getError();
        if (empty($error)) {
            return;
        }

        //die("dispatch error listener");

        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof Response) {
            return;
        }

        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                // Specifically not handling these
                return;

            case Application::ERROR_EXCEPTION:
            default:

                $exception = $e->getParam('exception');
                $modelData = array(
                    'message' => $exception->getMessage(),
                    'type' => get_class($exception)
                );

                //if ($this->displayExceptions()) {
                //    $modelData['exception'] = $exception;
                //}
                $code = 500;
                $e->setResult(new ApiProblem($code, $e));

                //$e->stopPropagation(true);
                //$e->setError(false);

                $response = $e->getResponse();
                if (!$response) {
                    $response = new HttpResponse();
                    $e->setResponse($response);
                }
                $response->setStatusCode(500);
                break;
        }
    }
}
