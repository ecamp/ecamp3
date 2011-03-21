<?php

namespace Controller;

class BaseController extends \Zend_Controller_Action
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 * @Inject EntityManager
	 */
	protected $em;
	
	/**
	 * @var \Zend_Session_Namespace
	 */
	protected $authSession;


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
		$this->view->addHelperPath(APPLICATION_PATH . '/../application/views/helpers', 'Application\View\Helper\\');
		$this->view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');

		$this->view->jQuery()->setLocalPath('/js/jquery-1.5.1.min.js');
		$this->view->jQuery()->setUiLocalPath('/js/jquery-ui-1.8.11.custom.min.js');
		$this->view->jQuery()->addStyleSheet('/css/jqueryui/smoothness/jquery-ui-1.8.11.custom.css');
		$this->view->jQuery()->enable();

		\Zend_Registry::get('kernel')->InjectDependencies($this);

		/* clone request params for debugging */
		$this->view->params = $this->getRequest()->getParams();
		
		$this->authSession = new \Zend_Session_Namespace('Zend_Auth');

		/** @var $login \Entity\Login */
		$login = $this->em->getRepository("Entity\Login")->find($this->authSession->Login);
		if( isset($login) )
		{
			$this->me = $login->getUser();
			$this->view->me = $this->me;
		}

		/* load translator */
		$this->t = new \Zend_View_Helper_Translate();
	}

}
