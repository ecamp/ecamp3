<?php

namespace WebApp\Navigation;


class Group extends \Zend_Navigation
{
	private static $pages = array(
			array(
			'label'      => 'Overview',
			'title'      => 'Overview',
			'controller' => 'group',
			'action'     => 'show'),

		    array(
			'label'      => 'Camps / Courses',
			'title'      => 'Camps / Courses',
			'controller' => 'group',
			'action'     => 'camps'),

		    array(
			'label'      => 'Members',
			'title'      => 'Members',
			'controller' => 'group',
			'action'     => 'members')
	    );
	
	
	public function __construct(\Core\Entity\Group $group)
	{
		parent::__construct(self::$pages);
		
		$params = array('group' => $group->getId());
		$options['route'] = 'group';
		
		foreach($this->getPages() as $page)
		{	
			$page->setParams($params);
			$page->setOptions($options);
		}
	}
}