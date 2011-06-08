<?php

namespace Navigation;


class Camp extends \Zend_Navigation
{
	private static $pages = array(
			array(
			'label'      => 'General',
			'title'      => 'General',
			'controller' => 'camp',
			'action'     => 'show',
			'route'		 => 'group+camp'),

		    array(
			'label'      => 'Leader',
			'title'      => 'Leader',
			'controller' => 'leader',
			'action'     => 'index',
			'route'		 => 'group+camp'),

		    array(
			'label'      => 'Events',
			'title'      => 'Events',
			'controller' => 'event',
			'action'     => 'index',
			'route'		 => 'group+camp'),

		    array(
			'label'      => 'Print',
			'title'      => 'Print',
			'controller' => 'camp',
			'action'     => 'print',
			'route'		 => 'group+camp')
	    );
	
	
	public function __construct(\Entity\Camp $camp)
	{
		parent::__construct(self::$pages);
		
		$params = array('camp' => $camp->getId());
		
		if(!is_null($camp->getOwner()))
		{	$params['user'] = $camp->getOwner()->getId();	}
			
		if(!is_null($camp->getGroup()))
		{	$params['group'] = $camp->getGroup()->getId();	}
		
		
		foreach($this->getPages() as $page)
		{	$page->setParams($params);	}
		
	}
}