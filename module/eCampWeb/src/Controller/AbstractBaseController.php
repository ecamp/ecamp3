<?php

namespace eCamp\Web\Controller;

use eCamp\Lib\Auth\AuthRequiredException;
use Zend\Authentication\AuthenticationService;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use ZF\Hal\Entity;
use ZF\Hal\View\HalJsonModel;

class AbstractBaseController extends \eCamp\Core\Controller\AbstractBaseController {
    public function attachDefaultListeners() {
        parent::attachDefaultListeners();

        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'setViewModelTerminal'], -90);
    }

    public function setViewModelTerminal(MvcEvent $e) {
        $vm = $e->getResult();

        if ($vm instanceof ViewModel) {
            $vm->setTerminal(true);
        }
    }


    public function onDispatch(MvcEvent $e) {
        $showRouteMatch = !!$this->params()->fromQuery('route-match', false);

        if(!$showRouteMatch) {
            // Shortcut: just add &rm to URL
            $q = $e->getRequest()->getUri()->getQuery();
            $q = explode('&', $q);
            $showRouteMatch = in_array('rm', $q);
        }

        if ($showRouteMatch) {
            $params = $this->params()->fromRoute();
            // Remove Controller & Action
            unset($params['controller']);
            unset($params['action']);
            // Remove objects (e.g. Entities)
            $params = array_filter($params, function($p) { return !is_object(($p)); });

            $result = new HalJsonModel();
            $result->setPayload(new Entity($params));
            $result->setTerminal(true);
            $e->setResult($result);

            return $result;
        }


        try {
            $result = parent::onDispatch($e);
        } catch (AuthRequiredException $ex) {
            /** @var Request $req */
            $req = $this->getRequest();
            $url = $req->getRequestUri();

            return $this->redirect()->toRoute(
                'ecamp.web/login',
                [],
                [ 'query' => [ 'redirect' => $url ] ]
            );
        }

        return $result;
    }

    /**
     * @throws AuthRequiredException
     */
    public function forceLogin() {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            throw new AuthRequiredException();
        }
    }
}
