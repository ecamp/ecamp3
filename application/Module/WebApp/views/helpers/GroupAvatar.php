<?php

namespace WebApp\View\Helper;

class GroupAvatar extends \Zend_View_Helper_Abstract
{
	/**
	 * @var \CoreApi\Service\Avatar
	 * @Inject \CoreApi\Service\Avatar
	 */
	protected $avatarService;
	
	public $view;

	public function __construct()
	{
		\Zend_Registry::get('kernel')->InjectDependencies($this);
	}
	
	public function __call($name, $arguments)
	{
		if( $name == "GroupAvatar" )
			return call_user_func_array(array($this,"execute"), $arguments);
	}
	
	/**
	 * this helper automatically adds the camp parameter to the url
	 */
    public function execute($groupId)
    {
		list($imageData, $imageMime) = $this->avatarService->getGroupAvatar($groupId);
		
		$base64 = base64_encode($imageData);
		return "data:$imageMime;base64,$base64";
    }

    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}

