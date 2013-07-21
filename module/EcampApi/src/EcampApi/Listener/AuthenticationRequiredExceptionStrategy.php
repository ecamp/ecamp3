<?php
namespace EcampApi\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\Mvc\View\Http\ExceptionStrategy;
use Zend\Mvc\MvcEvent;
use EcampLib\Acl\Exception\AuthenticationRequiredException;

/**
 *
 * @since   1.0
 * @author  Tim Roediger <superdweebie@gmail.com>
 */
class AuthenticationRequiredExceptionStrategy extends ExceptionStrategy
{

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'redirectToApiLogin'), 200);
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
    public function redirectToApiLogin(MvcEvent $e)
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

        if ($error == Application::ERROR_EXCEPTION) {
            $exception = $e->getParam('exception');
            if ($exception instanceof AuthenticationRequiredException) {

                  $routename = $e->getRouteMatch()->getMatchedRouteName();
                   if (substr($routename, 0, 4) == 'api/') {
                       $url = $e->getRouter()->assemble(
                           array('controller' => 'login', ''),
                           array('name' => 'api/default')
                       );

                       $response = $e->getResponse();
                       $response->getHeaders()->addHeaderLine('Location', $url);
                       $response->setStatusCode(302);
                       $response->sendHeaders();
                       exit;
                   }
            }
        }
    }
}
