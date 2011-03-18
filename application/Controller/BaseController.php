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
	 * @var Zend_Session_Namespace
	 */
	protected $authSession;

	/** loggedin user */
	protected $me = null;
	
	public function init()
	{
		$this->view->addHelperPath(APPLICATION_PATH . '/../application/views/helpers', 'Application\View\Helper\\');

		\Zend_Registry::get('kernel')->InjectDependencies($this);

		/* clone request params for debugging */
		$this->view->params = $this->getRequest()->getParams();
		
		$this->authSession = new \Zend_Session_Namespace('Zend_Auth');
		
		$login = $this->em->getRepository("Entity\Login")->find($this->authSession->Login);
		if( isset($login) )
		{
			$this->me = $login->getUser();
			$this->view->me = $this->me;
		}
	}

}
