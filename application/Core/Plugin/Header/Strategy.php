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
 
/**
 * Every pluginstrategy is *only* responsible for rendering a plugin and declaring some basic
 * support, but *not* for updating its configuration etc. For this purpose, use controllers
 * and models.
 */
 
namespace Core\Plugin\Header;

class Strategy extends \Core\Plugin\AbstractStrategy implements \Core\Plugin\IPluginStrategy {
	
	/**
	 * @var \Core\Plugin\Header\Entity\Header $plugin
	 */
	protected $header;
	
	protected $pluginName = "Header";
	
	/** construct */
	public function __construct( \Doctrine\ORM\EntityManager $em, \Zend_View_Interface $view, \Core\Entity\Plugin $plugin)
	{
		$this->em = $em;
		$this->view = $view;
		$this->plugin = $plugin;
		
		$this->header = new \Core\Plugin\Header\Entity\Header($plugin);
	}
	
	/**
	 * persist all child objects
	 */
	public function persist()
	{
		return $this->em->persist($this->header);
	}
	
	/**
	 * remove all child objects
	 */
	public function remove()
	{
		return $this->em->remove($this->header);
	}

	/**
	 * load Objects from EntityManager
	 */
	public function loadObjects()
	{
		$this->header = $this->em->getRepository('Core\Plugin\\'.$this->pluginName.'\Entity\Header')->findOneBy( array('plugin' => $this->plugin->getId()) );
	}
	
	/**
	 * Set the HeaderPlugin object.
	 * @param \Core\Plugin\Header\Entity\Header $header
	 */
	public function setHeader($header)
	{
		$this->header = $header;
	}
	
	/**
	 * Get the HeaderPlugin Object
	 * @return \Core\Plugin\Header\Entity\Header
	 */
	public function getHeader()
	{
		return $this->header;
	}
	
}
