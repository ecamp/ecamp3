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
use Doctrine\DBAL\Types\StringType;

class Content extends \Core\Entity\BaseEntity 
{

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
	 * @var string
	 * @Column(type="string", length=64, nullable=true)
	 */
	private $text;

	/**
	 * @var \Core\Entity\Plugin
	 * @ManyToOne(targetEntity="Core\Entity\Plugin")
	 */
	private $plugin;


	/**
	 * Get ContentPlugin Id
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set ContentPlugin Text
	 * @param string $text
	 */
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * Get ContentPlugin Text
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}


	/**
	 * Set Plugin Object
	 * @param \Core\Entity\Plugin $plugin
	 */
	public function setPlugin(\Core\Entity\Plugin $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * Get Plugin Object
	 * @return \Core\Entity\Plugin
	 */
	public function getPlugin()
	{
		return $this->plugin;
	}

}