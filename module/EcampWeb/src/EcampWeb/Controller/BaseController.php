<?php

namespace EcampWeb\Controller;

use EcampCore\Controller\AbstractBaseController;
use EcampCore\Entity\User;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Class BaseController
 * @method Request getRequest()
 * @method Response getResponse()
 */
abstract class BaseController
    extends AbstractBaseController
{
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $events->attach('dispatch', function($e) { $this->setMeInViewModel($e); } , -100);
        $events->attach('dispatch', function($e) { $this->setConfigInViewModel($e); } , -100);
    }

    public function onDispatch( MvcEvent $e )
    {
        $this->getServiceLocator()->get('Twig_Environment')->getExtension('core')->setDateFormat('d.m.Y');

        parent::onDispatch($e);
    }

    /**
     * @param MvcEvent $e
     */
    private function setMeInViewModel(MvcEvent $e)
    {
        $result = $e->getResult();

        if ($result instanceof ViewModel) {
            $me = $this->getMe() ?: User::ROLE_GUEST;
            $acl = $this->getServiceLocator()->get('EcampCore\Acl');

            $result->setVariable('me', $me);
            $result->setVariable('acl', $acl);
        }
    }

     /**
     * @param MvcEvent $e
     */
    private function setConfigInViewModel(MvcEvent $e)
    {
        $result = $e->getResult();

        if ($result instanceof ViewModel) {
            $config = $this->getServiceLocator()->get('config');

            $result->setVariable('config', $config['ecamp']);
        }
    }

    protected function getRedirectResponse($url)
    {
        /** @var $renderer \Zend\View\Renderer\RendererInterface */
        $renderer = $this->getServiceLocator()->get('ZfcTwigRenderer');

        $viewModel = new ViewModel();
        $viewModel->setTemplate('ecamp-web/redirect');
        $viewModel->setVariable('url', $url);

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setContent($renderer->render($viewModel));

        return $response;
    }

}
