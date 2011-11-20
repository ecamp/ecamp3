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
	 * @var \Doctrine\ORM\EntityManager
	 * @Inject EntityManager
	 */
	protected $em;

	/**
	 * logged in user
	 * @var \Entity\User
	 */
	protected $me;

	/**
	 * default translator
	 * @var \Zend_Translate
	 */
	protected $t;
	
	
	
	public function init()
	{
		
		
		$this->view->addHelperPath(APPLICATION_PATH . '/Module/WebApp/views/helpers', '\\WebApp\View\Helper\\');
		$this->view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');

		$this->view->jQuery()->setLocalPath('/js/jquery-1.5.1.min.js');
		$this->view->jQuery()->setUiLocalPath('/js/jquery-ui-1.8.11.custom.min.js');
		$this->view->jQuery()->addStyleSheet('/css/jqueryui/smoothness/jquery-ui-1.8.11.custom.css');
		$this->view->jQuery()->enable();
		
		$this->view->headScript()->appendFile('/js/jquery.form.js');

		//die(print_r(\Zend_Registry::get('kernel')));
		\Zend_Registry::get('kernel')->InjectDependencies($this);

		/* clone request params for debugging */
		$this->view->params = $this->getRequest()->getParams();

        
        if(\Zend_Auth::getInstance()->hasIdentity())
        {
            $userId = \Zend_Auth::getInstance()->getIdentity();

            /** @var $login \Entity\Login */
            $user = $this->em->getRepository("Core\Entity\User")->find($userId);
            if( isset($user) )
            {
                $this->me = $user;
                $this->view->me = $this->me;
            }
        }




		/* load translator */
		$this->t = new \Zend_View_Helper_Translate();
	}
	
	public function postDispatch()
	{
		if( $this->em->isOpen() )
			$this->em->flush();
	}

	
	protected function setNavigation(\Zend_Navigation $navigation)
	{
		$this->view->getHelper('navigation')->setContainer($navigation);
	}
}
