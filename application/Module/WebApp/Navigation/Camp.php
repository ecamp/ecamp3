<?php

namespace WebApp\Navigation;


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
			'action'     => 'index'),

		    array(
			'label'      => 'Events',
			'title'      => 'Events',
			'controller' => 'event',
			'action'     => 'index'),

		    array(
			'label'      => 'Print',
			'title'      => 'Print',
			'controller' => 'camp',
			'action'     => 'print')
	    );
	
	
	public function __construct(\Core\Entity\Camp $camp)
	{
		parent::__construct(self::$pages);
		
		$params = array('camp' => $camp->getId());
		
		if(!is_null($camp->getOwner()))
		{	
			$params['user'] = $camp->getOwner()->getId();
			$options['route'] = 'user+camp';
		}
			
		if(!is_null($camp->getGroup()))
		{	
			$params['group'] = $camp->getGroup()->getId();	
			$options['route'] = 'group+camp';
		}
		
		foreach($this->getPages() as $page)
		{	
			$page->setParams($params);	
			$page->setOptions($options);
		}
		
	}
}
