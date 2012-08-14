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

namespace CoreApi\Entity;

/**
 * @Entity
 * @Table(name="images")
 */
class Image extends BaseEntity
{
	
	/**
	 * @Column(type="string", length=32, nullable=false)
	 */
	private $imageMime;
	
	/**
	 * @Column(type="object", nullable=false)
	 */
	private $imageData;


	
	/**
	 * @return string
	 */
	public function getData()
	{
		return base64_decode($this->imageData);
	}
	
	/**
	 * @return CoreApi\Entity\Image
	 */
	public function setData($data)
	{
		$this->imageData = base64_encode($data); return $this;
	}
	
	
	/**
	 * @return string
	 */
	public function getMime()
	{
		return $this->imageMime;
	}
	
	/**
	 * @return CoreApi\Entity\Image
	 */
	public function setMime($mime)
	{
		$this->imageMime = $mime; return $this;
	}
	
}
