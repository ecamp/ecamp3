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

        $appAutoloader = new \Doctrine\Common\ClassLoader('eCamp');
        $autoloader->pushAutoloader(array($appAutoloader, 'loadClass'), 'eCamp');


		
        $appAutoloader = new \Doctrine\Common\ClassLoader('Inject');
        $autoloader->pushAutoloader(array($appAutoloader, 'loadClass'), 'Inject');
    }



	public function _initInjectionKernel()
	{
		$kernel = new \Inject\Kernel();

		$kernel
			->Bind("EntityManager")
			->ToProvider(new eCamp\Provider\EntityManager());

		$kernel
			->Bind("CampRepository")
			->ToProvider(new eCamp\Provider\Repository("eCamp\Entity\Camp"));

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
			'UserId', new Zend_Controller_Router_Route('/doctrine/index/:UserId',
				array('controller' => 'doctrine', 'action' => 'index')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'LoginLoginId', new Zend_Controller_Router_Route('/login/login/:Id',
				array('controller' => 'login', 'action' => 'login')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
					'EditCamp', new Zend_Controller_Router_Route('/camp/editcamp/:Id',
						array('controller' => 'camp', 'action' => 'editcamp')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'CommitToCamp', new Zend_Controller_Router_Route('/camp/commitToCamp/:Id',
				array('controller' => 'camp', 'action' => 'commitToCamp')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'CancelFromCamp', new Zend_Controller_Router_Route('/camp/cancelFromCamp/:Id',
				array('controller' => 'camp', 'action' => 'cancelFromCamp')));

	}
}

