<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
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

namespace Core\Plugin;

use CoreApi\Entity\PluginPrototype;

class RenderPluginPrototype
{
	/**
	 * @var RenderContainer
	 */
	private $renderContainer;
	
	/**
	 * @var CoreApi\Entity\PluginPrototype
	 */
	private $pluginPrototype;
	
	
	private $renderPluginInstances = array();
	
	
	
	public function __construct(RenderContainer $renderContainer, PluginPrototype $pluginPrototype){
		$this->renderContainer = $renderContainer;
		$this->pluginPrototype = $pluginPrototype;
		
		$this->renderContainer->addRenderPluginPrototype($this);
	}
	
	
	/**
	 * @access private
	 */
	public function addRenderPluginInstance(RenderPluginInstance $renderPluginInstance){
		return $this->renderPluginInstances[] = $renderPluginInstance;
	}
	
	/**
	 * @return \Core\Plugin\RenderContainer
	 */
	public function getRenderContainer(){
		return $this->renderContainer;
	}
	
	public function getRenderPluginInstances(){
		return $this->renderPluginInstances;
	}
	
	public function isInstanceAddable(){
		return $this->pluginPrototype->getMaxInstances() > count($this->renderPluginInstances);
	}
	
	public function isInstanceDeletable(){
		return $this->pluginPrototype->getMinInstances() < count($this->renderPluginInstances);
	}
}