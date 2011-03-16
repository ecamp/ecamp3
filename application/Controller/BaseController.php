<?php

namespace Controller;

class BaseController extends \Zend_Controller_Action
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 * @Inject EntityManager
	 */
	protected $em;

	public function init()
	{
		$this->view->addHelperPath(APPLICATION_PATH . '/../application/views/helpers', 'Application\View\Helper\\');

		\Zend_Registry::get('kernel')->InjectDependencies($this);
	}

}
