<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
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

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	/**
	 * @return void
	 */
	public function _initAutoloader()
    {
        require_once APPLICATION_PATH . '/../library/Doctrine/Common/ClassLoader.php';

        $autoloader = \Zend_Loader_Autoloader::getInstance();

        $bisnaAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
        $autoloader->pushAutoloader(array($bisnaAutoloader, 'loadClass'), 'Bisna');

        $appAutoloader = new \Doctrine\Common\ClassLoader('Inject');
        $autoloader->pushAutoloader(array($appAutoloader, 'loadClass'), 'Inject');

		$entityAutoloader = new \Doctrine\Common\ClassLoader('Entity', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($entityAutoloader, 'loadClass'), 'Entity');

	    $controllerAutoloader = new \Doctrine\Common\ClassLoader('Controller', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($controllerAutoloader, 'loadClass'), 'Controller');

		$providerAutoloader = new \Doctrine\Common\ClassLoader('Logic', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($providerAutoloader, 'loadClass'), 'Logic');

		$entityAutoloader = new \Doctrine\Common\ClassLoader('Service', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($entityAutoloader, 'loadClass'), 'Service');

    }

	public function _initInjectionKernel()
	{
		$kernel = new \Inject\Kernel();

		$kernel
			->Bind("EntityManager")
			->ToProvider(new Logic\Provider\EntityManager());

		$kernel
			->Bind("CampRepository")
			->ToProvider(new Logic\Provider\Repository("\Entity\Camp"));

		$kernel
			->Bind("LoginRepository")
			->ToProvider(new Logic\Provider\Repository("\Entity\Login"));

		$kernel
			->Bind("UserRepository")
			->ToProvider(new Logic\Provider\Repository("\Entity\User"));

		$kernel
			->Bind("UserToCampRepository")
			->ToProvider(new Logic\Provider\Repository("\Entity\UserToCamp"));


		$kernel->Bind("SomeService")->ToSelf()->AsSingleton();

		Zend_Registry::set("kernel", $kernel);
	}

	/**
	 * Override the default Zend_View with Ztal support and configure defaults.
	 *
	 * @return void
	 */
	protected function _initZtal()
	{
		//register the Ztal plugin
		$plugin = new Ztal_Controller_Plugin_Ztal($this->getOption('ztal'));
		Zend_Controller_Front::getInstance()->registerPlugin($plugin);
	}


	protected function _initRoutes()
	{

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'general', new Zend_Controller_Router_Route(':controller/:action/*',
			array('controller' => 'index', 'action' => 'index')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'shortid', new Zend_Controller_Router_Route(':controller/:action/:id/*',
			array('controller' => 'index', 'action' => 'index'),
			array('id' => '\d+')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'camp', new Zend_Controller_Router_Route(':camp/:controller/:action/*',
			array('controller' => 'index', 'action' => 'index'),
			array('camp' => '\d+')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'camp_shortid', new Zend_Controller_Router_Route(':camp/:controller/:action/:id/*',
			array('controller' => 'index', 'action' => 'index'),
			array('camp' => '\d+', 'id' => '\d+')));

	}

	protected function _initView()
	{
		$view = new Zend_View();

		$view->setEncoding('UTF-8');
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');

		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);

		return $view;
	}    
}

