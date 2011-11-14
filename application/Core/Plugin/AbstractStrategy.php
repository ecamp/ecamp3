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
 
abstract class AbstractStrategy {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * @var Core\Entity\Plugin
	 */
	protected $plugin;
	
	/**
	 * Set the plugin object.
	 */
	public function setPlugin(\Core\Entity\Plugin $plugin){
		$this->plugin = $plugin;
	}
	
	/**
	 * Get the plugin object.
	 */
	public function getPlugin(){
		return $this->plugin;
	}
	
	
	/* ***************************************** */
	/* don't know whether we need the view here. 
	   It was here in the doctrine example, but I couldn't
	   figure out why.
	   Let's leave it here until the full plugin stack has
	   been implemented */
	   
	protected $view;
	
	/**
	 * Set the view object.
	 * @param  \Zend_View_Interface $view
	 * @return \Zend_View_Helper_Interface
	 */
	public function setView(\Zend_View_Interface $view){
		$this->view = $view;
	}

	/**
	 * @return \Zend_View_Interface
	 */
	public function getView(){
		return $this->view;
	}

}