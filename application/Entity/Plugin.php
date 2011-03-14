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

namespace Entity;

/**
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="plugin", type="string")
 * @DiscriminatorMap({"plugin" = "Plugin", "header" = "PluginHeader", "doc" = "PluginDocument"})
 */
class Plugin extends BaseEntity
{

	/**
	 * @var int
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="Event")
	 * @JoinColumn(nullable=false)
	 */
	public $event;


	
	public function getId(){ return $this->id; }

	public function setEvent(Event $event){ $this->event = $event; }
	public function getEvent()            { return $this->event;   }
	
}
