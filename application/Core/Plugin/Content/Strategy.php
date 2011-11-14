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
 
namespace Core\Plugin\Content;

class Strategy extends \Core\Plugin\AbstractStrategy implements \Core\Plugin\IPluginStrategy {
	
	/**
	 * Core\Plugin\Content\Entity\Content $plugin
	 */
	private $content;
	
	public $pluginName = "Content";
	
	/** construct */
	public function __construct( \Doctrine\ORM\EntityManager $em, \Zend_View_Interface $view, \Core\Entity\Plugin $plugin) {
		$this->em = $em;
		$this->view = $view;
		$this->plugin = $plugin;
		
		$this->content = new \Core\Plugin\Content\Entity\Content($plugin);
	}
	
	/**
	 * persist all child objects
	 */
	public function persist()
	{
		return $this->em->persist($this->content);
	}
	
	/**
	 * remove all child objects
	 */
	public function remove()
	{
		return $this->em->remove($this->content);
	}

	/**
	 * load Objects from EntityManager
	 */
	public function loadObjects()
	{
		$this->content = $this->em->getRepository('Core\Plugin\\'.$this->pluginName.'\Entity\Content')->findOneBy( array('plugin' => $this->plugin->getId()) );
	}
	
	/**
	 * Set the plugin object.
	 */
	public function setContent($content){
		$this->content = $content;
	}
	public function getContent(){
		return $this->content;
	}

	/**
	 * Renders this strategy. This method will be called when the user
	 * displays the site.
	 *
	 * @return string
	 */
	public function renderFrontend(){
		return $this->content->getText();
	}

	/**
	 * Renders the backend of this plugin. This method will be called when
	 * a user tries to reconfigure this plugin instance.
	 *
	 * Most of the time, this method will return / output a simple form which in turn
	 * calls some controllers.
	 *
	 * @return string
	 */
	public function renderBackend(){
		$this->view->content = $this->content;
		$this->view->plugin = $this->plugin;
		return $this->view->render("../Plugin/".$this->pluginName."/views/edit.phtml");
	}
	
}
