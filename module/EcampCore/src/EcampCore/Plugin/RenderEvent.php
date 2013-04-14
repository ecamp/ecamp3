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

use CoreApi\Entity\Event;
use CoreApi\Entity\Medium;
use CoreApi\Entity\EventTemplate;

class RenderEvent
{
	
	private $renderContainers = array();
	
	/**
	 * @var CoreApi\Entity\Event
	 */
	private $event;
	
	/**
	 * @var CoreApi\Entity\Medium
	 */
	private $medium;
	
	/**
	 * @var CoreApi\Entity\EventTemplate
	 */
	private $template;
	
	/**
	 * @var boolean
	 */
	private $backend;
	
	
	public function __construct(Event $event, Medium $medium, EventTemplate $template, $backend = false){
		$this->event = $event;
		$this->medium = $medium;
		$this->template = $template;
		$this->backend = !!$backend;
	}
	
	public function getEvent(){
		return $this->event;
	}
	
	public function getMedium(){
		return $this->medium;
	}
	
	public function getTemplate(){
		return $this->template;
	}
	
	public function isBackend(){
		return $this->backend;
	}
	
	public function isFrontend(){
		return !$this->backend;
	}
	
	/**
	 * @access private
	 */
	public function addRenderContainer(RenderContainer $renderContainer){
		return $this->renderContainers[$renderContainer->getContainerName()] = $renderContainer;
	}
	
	public function getRenderContainer($containerName){
		return $this->renderContainers[$containerName];
	}
	
	public function getRenderContainers(){
		return $this->renderContainers;
	}
	
	
	
}