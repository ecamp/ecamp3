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
 
namespace WebApp\Plugin\Header;

class Strategy extends \Core\Plugin\Header\Strategy implements \Core\Plugin\IPluginStrategy {

	/**
	 * Renders this strategy. This method will be called when the user
	 * displays the site.
	 *
	 * @return string
	 */
	public function renderFrontend(){
		return $this->header->getText();
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
		$this->view->header = $this->header;
		$this->view->plugin = $this->plugin;
		return $this->view->render("../Plugin/".$this->pluginName."/views/edit.phtml");
	}
	
}
