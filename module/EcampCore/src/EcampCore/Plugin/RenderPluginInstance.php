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

use CoreApi\Entity\PluginInstance;

class RenderPluginInstance
{
	
	/**
	 * @var RenderPluginPrototype
	 */
	private $renderPluginPrototype;
	
	/**
	 * @var CoreApi\Entity\PluginInstance
	 */
	private $pluginInstance;
	
	
	public function __construct(RenderPluginPrototype $renderPluginPrototype, PluginInstance $pluginInstance){
		$this->renderPluginPrototype = $renderPluginPrototype;
		$this->pluginInstance = $pluginInstance;
		
		$this->renderPluginPrototype->addRenderPluginInstance($this);
	}
	
	public function getRenderPluginPrototype(){
		return $this->renderPluginPrototype;
	}
	
	/**
	 * @return \CoreApi\Entity\PluginInstance
	 */
	public function getPluginInstance(){
		return $this->pluginInstance;
	}
	
	public function isInstanceDeletable(){
		return $this->renderPluginPrototype->isInstanceDeletable();
	}
	
	public function getPluginName(){
		return $this->getPluginInstance()->getPluginPrototype()->getPlugin()->getName();
	}
	
	
	public function render($medium = null, $backend = null){
		
		if($this->renderPluginPrototype == null && $medium == null){
			throw new Exception("Cannot render a single PluginInstance without defining the MEDIUM");
		}
		if($this->renderPluginPrototype == null && $backend == null){
			throw new Exception("Cannot render a single PluginInstance without defining whether to render Front- or Backend");
		}
		
		if($this->renderPluginPrototype != null){
			$renderEvent = $this->renderPluginPrototype->getRenderContainer()->getRenderEvent();
			$medium = $medium ?: $renderEvent->getMedium();
			$backend = $backend ?: $renderEvent->isBackend();
		}
		
		$pluginName = $this->getPluginInstance()->getPluginPrototype()->getPlugin()->getName();
		
		$view = new \Ztal_Tal_View();
		$view->doctype('XHTML1_TRANSITIONAL');
		
		$view->addTemplateRepositoryPath(APPLICATION_PATH . "/Plugin/" . $pluginName . "/views/" . $medium->getName());
		$view->addHelperPath(APPLICATION_PATH . '/Module/WebApp/views/helpers', '\\WebApp\View\Helper\\');
		
		if($backend){
			return $this->getPluginInstance()->getStrategyInstance()->renderBackend($view);
		} else{
			return $this->getPluginInstance()->getStrategyInstance()->renderFrontend($view);
		}
	}
}