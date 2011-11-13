<?php

namespace WebApp\Navigation;


class Dashboard extends \Zend_Navigation
{
	private static $pages = 
		array(
			array(
			'label'      => 'Dashboard',
			'title'      => 'Dashboard',
			'controller' => 'dashboard',
			'action'     => 'index',
			'pages' => array(
				
				array(
				'label'      => 'subitem',
				'title'      => 'subitem for nothing else than for testing whether the menu shows it or not',
				'controller' => 'dashboard',
				'action'     => 'subitem'),
				
				array(
				'label'      => 'subitem2',
				'title'      => 'subitem for nothing else than for testing whether the menu shows it or not',
				'controller' => 'dashboard',
				'action'     => 'subitem2')
			)),
			
			array(
			'label'      => 'Camps',
			'title'      => 'Camps',
			'controller' => 'dashboard',
			'action'     => 'camps'),
			
			array(
			'label'      => 'Friends',
			'title'      => 'Friends',
			'controller' => 'dashboard',
			'action'     => 'friends'),
			
			array(
			'label'      => 'Groups',
			'title'      => 'Groups',
			'controller' => 'dashboard',
			'action'     => 'groups'));
	
	
	public function __construct()
	{
		parent::__construct(self::$pages);
	}
}