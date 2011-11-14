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
 * This interface defines the basic actions that a plugin needs to support.
 *
 * Every pluginstrategy is *only* responsible for rendering a plugin and declaring some basic
 * support, but *not* for updating its configuration etc. For this purpose, use controllers
 * and models.
 */
namespace Core\Plugin;
 
interface IPluginStrategy {

    /**
	 * construct
	 */
	public function __construct( \Doctrine\ORM\EntityManager $em, \Zend_View_Interface $view, \Core\Entity\Plugin $plugin );

	/**
	 * Persist all child objects
	 */
	public function persist();
	
	/**
	 * Remove all child objects
	 */
	public function remove();
	
	/**
	 * Load entities that belong to this strategy
	 */
	public function loadObjects();
	
	/**
	 * Set the view object.
	 * @param  \Zend_View_Interface $view
	 * @return \Zend_View_Helper_Interface
	 */
	public function setView(\Zend_View_Interface $view);

	/**
	 * @return \Zend_View_Interface
	 */
	public function getView();

	/**
	 * Renders this strategy (read only)
	 *
	 * @return string
	 */
	public function renderFrontend();

	/**
	 * Renders the backend of this plugin (write access)
	 *
	 * @return string
	 */
	public function renderBackend();


	public function setPlugin(\Core\Entity\Plugin $plugin);

	public function getPlugin();
}