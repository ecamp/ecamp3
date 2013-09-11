<?php
namespace EcampApi\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\Mvc\View\Http\ExceptionStrategy;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

/**
 *
 * @since   1.0
 * @author  Tim Roediger <superdweebie@gmail.com>
 */
class JsonExceptionStrategy extends ExceptionStrategy
{

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'prepareExceptionViewModel'), 100);
    }

    /**
     * Create an exception json view model, and set the HTTP status code
     *
     * @todo   dispatch.error does not halt dispatch unless a response is
     *         returned. As such, we likely need to trigger rendering as a low
     *         priority dispatch.error event (or goto a render event) to ensure
     *         rendering occurs, and that munging of view models occurs when
     *         expected.
     * @param  MvcEvent $e
     * @return void
     */
    public function prepareExceptionViewModel(MvcEvent $e)
    {
        // Do nothing if no error in the event
        $error = $e->getError();
        if (empty($error)) {
            return;
        }

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

                if ($this->displayExceptions()) {
                    $modelData['exception'] = $exception;
                }
                $e->setResult(new JsonModel($modelData));
                $e->setError(false);

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
