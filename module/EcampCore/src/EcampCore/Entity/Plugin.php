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
 * This is the base class for both Panels and Plugins.
 * It shouldn't be extended by your own plugins - simply write a strategy!
 */

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="plugins")
 */
class Plugin extends BaseEntity
{

	/**
	 * @ORM\Column(type="string", length=64, nullable=false)
	 */
	private $name;
	
	
	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 */
	private $active;
	
	
	/**
	 * @ORM\Column(type="string", length=128, nullable=false)
	 */
	private $strategyClass;
	
	
	public function getName(){
		return $this->name;
	}
	
	
	public function getActive(){
		return $this->active;
	}
	
	public function setActive($active){
		$this->active = $active;
	}
	
	
	public function getStrategyClass(){
		return $this->strategyClass;
	}
}