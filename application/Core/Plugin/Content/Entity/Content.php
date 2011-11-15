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
 
namespace Core\Plugin\Content\Entity;

/**
 * @Entity
 * @Table(name="plugin_contents")
 */
class Content extends \Core\Entity\BaseEntity {
	
	public function __construct($plugin)
	{
		$this->text = "hello world";
		$this->plugin = $plugin;
	}
	
	/**
	 * The id of the plugin item instance
	 * This is a doctrine field, so you need to setup generation for it
	 * @var integer
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @Column(type="string", length=64, nullable=true)
	 */
	private $text;
	
	/**
	 * @ManyToOne(targetEntity="Core\Entity\Plugin")
	 */
	private $plugin;
	

	public function getId(){ return $this->id; }

	public function setText($text){ $this->text = $text; }
	public function getText()     { return $this->text; }
	
	public function setPlugin($plugin){ $this->plugin = $plugin; }
	public function getPlugin()     { return $this->plugin; }
	
}