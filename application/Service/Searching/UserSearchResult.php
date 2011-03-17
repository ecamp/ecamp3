<?php
/**
 *
 * Copyright (C) 2011 pirminmattmann
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
 *
 */

namespace Service\Searching;


class UserSearchResult
{
	/**
	 * @var \Entity\User
	 */
	private $user;


	/**
	 * @var array
	 */
	private $matches;





	public function __construct(\Entity\User $user)
	{
		$this->user = $user;

		$this->matches = array();
	}



	public function found($by, $matchLength)
	{
		if(array_key_exists($by, $this->matches))
		{
			if($matchLength <= $this->matches[$by])
			{	return;	}
		}

		$this->matches[$by] = $matchLength;
	}


}
