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
 * This is the base class for all plugin strategies
 *
 * Every pluginstrategy is *only* responsible for rendering a plugin and declaring some basic
 * support, but *not* for updating its configuration etc. For this purpose, use controllers
 * and models.
 */

namespace Core\Plugin;
 
abstract class AbstractStrategy 
{

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * @var CoreApi\Entity\Plugin
	 */
	protected $plugin;
	
	/**
	 * Set the plugin object.
	 * @param \CoreApi\Entity\Plugin $plugin
	 */
	public function setPlugin(\CoreApi\Entity\Plugin $plugin)
	{
		$this->plugin = $plugin;
	}
	
	/**
	 * Get the plugin object.
	 */
	public function getPlugin(){
		return $this->plugin;
	}
	
	/**
	 * Get the plugin name
	 */
	public function getPluginName(){
		return $this->pluginName;
	}
	
	/* ***************************************** */
	   
	protected $view;
	
	protected function createView()
	{
		$this->view = new \Ztal_Tal_View();
		$this->view->doctype('XHTML1_TRANSITIONAL');
		$this->view->addTemplateRepositoryPath(APPLICATION_PATH."/Module/WebApp/Plugin/".$this->getPluginName()."/views");
		$this->view->addHelperPath(APPLICATION_PATH . '/Module/WebApp/views/helpers', '\\WebApp\View\Helper\\');
	}

	/**
	 * @return \Zend_View_Interface
	 */
	public function getView()
	{
		return $this->view;
	}
	
	public function renderFrontend(){
	}
	
	public function renderBackend(){
	}

}