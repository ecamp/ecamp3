<?php
/*
 * Copyright (C) 2011 Urban Suppiger
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */
 
namespace WebApp\Controller;

class BaseController extends \Zend_Controller_Action
{

	/**
	 * logged in user
	 * @var CoreApi\Entity\User
	 */
	protected $me;

	/**
	 * default translator
	 * @var \Zend_Translate
	 */
	protected $t;
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	/**
	 * @var Core\Acl\DefaultAcl
	 * @Inject Core\Acl\DefaultAcl
	 */
	protected $acl;
	
	public function getContext(){
		return $this->contextProvider->getContext();
	}
	
	public function init()
	{
		
		$this->view->addTemplateRepositoryPath(APPLICATION_PATH."/CoreApi/Plugin/web");
		
		$this->view->addHelperPath(APPLICATION_PATH . '/Module/WebApp/views/helpers', '\\WebApp\View\Helper\\');
		$this->view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');

		$this->view->jQuery()->setLocalPath('/js/jquery-1.7.2.js');
		$this->view->jQuery()->setUiLocalPath('/js/jquery-ui-1.8.11.custom.min.js');
		$this->view->jQuery()->addStyleSheet('/css/jqueryui/smoothness/jquery-ui-1.8.11.custom.css');
		$this->view->jQuery()->enable();
		
		/* @TODO only temporary, for jQqueryUi 1.9 */
		$this->view->headScript()->appendFile("/js/jquery-ui-1.9/jquery.ui.position.js");
		$this->view->headScript()->appendFile("/js/jquery-ui-1.9/jquery.ui.core.js");
		$this->view->headScript()->appendFile("/js/jquery-ui-1.9/jquery.ui.widget.js");
		$this->view->headScript()->appendFile("/js/jquery-ui-1.9/jquery.ui.button.js");
		$this->view->headScript()->appendFile("/js/jquery-ui-1.9/jquery.ui.tabs.js");
		$this->view->headScript()->appendFile("/js/jquery-ui-1.9/jquery.ui.dialog.js");
		$this->view->headScript()->appendFile("/js/jquery-ui-1.9/jquery.ui.menu.js");

		$this->view->headScript()->appendFile('/js/jquery.form.js');

		//die(print_r(\Zend_Registry::get('kernel')));
		\Zend_Registry::get('kernel')->Inject($this);

		/* clone request params for debugging */
		$this->view->params = $this->getRequest()->getParams();
		
		$this->me = $this->contextProvider->getContext()->getMe();
		$this->view->me = $this->me;
		$this->view->context = $this->contextProvider->getContext();
		$this->view->roles   = $this->acl->getRolesInContext();
        

		/* load translator */
		$this->t = new \Zend_View_Helper_Translate();
	}
	
	protected function setNavigation(\Zend_Navigation $navigation)
	{
		$this->view->getHelper('navigation')->setContainer($navigation);
	}
	
	
	protected function forward($route, $action, $controller = null, $module = null, array $params = null)
    {
        $request = $this->getRequest();
		$request->clearParams();
        
        if (null !== $params) {
            $request->setParams($params);
        }

        if (null !== $controller) {
            $request->setControllerName($controller);
            $request->setParam($request->getControllerKey(), $controller);

            // Module should only be reset if controller has been specified
            if (null !== $module) {
                $request->setModuleName($module);
            	$request->setParam($request->getModuleKey(), $module);
            }
        }

        $request->setActionName($action);
        $request->setParam($request->getActionKey(), $action);
		$request->setDispatched(false);

		
		$router = \Zend_Controller_Front::getInstance()->getRouter();
		$url = $router->assemble($request->getParams(), $route, true);
		
		$this->view->BrowserUrlScript =
			"<script type='text/javascript'> window.history.replaceState({}, '', '$url'); </script>";
    }
}
