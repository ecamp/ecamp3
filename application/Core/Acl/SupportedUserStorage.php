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

namespace Core\Acl;


class SupportedUserStorage
{
	
	/**
	* Default session namespace
	*/
	const NAMESPACE_DEFAULT = 'eCamp_Core_Acl_Support';
	
	/**
	 * Default session object member name
	 */
	const MEMBER_DEFAULT = 'SupportedUserId';
	
	/**
	 * Object to proxy $_SESSION storage
	 *
	 * @var Zend_Session_Namespace
	 */
	protected $_session;
	
	/**
	 * Session namespace
	 *
	 * @var mixed
	 */
	protected $_namespace;
	
	/**
	 * Session object member
	 *
	 * @var mixed
	 */
	protected $_member;
	
	/**
	 * Sets session storage options and initializes session namespace object
	 *
	 * @param  mixed $namespace
	 * @param  mixed $member
	 * @return void
	 */
	public function __construct($namespace = self::NAMESPACE_DEFAULT, $member = self::MEMBER_DEFAULT)
	{
		$this->_namespace = $namespace;
		$this->_member    = $member;
		$this->_session   = new \Zend_Session_Namespace($this->_namespace);
	}
	
	
	
	/**
	 * Returns the session namespace
	 *
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->_namespace;
	}
	
	/**
	 * Returns the name of the session object member
	 *
	 * @return string
	 */
	public function getMember()
	{
		return $this->_member;
	}

	/**
	 * Defined by Zend_Auth_Storage_Interface
	 *
	 * @return boolean
	 */
	public function isEmpty()
	{
		return !isset($this->_session->{$this->_member});
	}
	
	/**
	 * Defined by Zend_Auth_Storage_Interface
	 *
	 * @return mixed
	 */
	public function read()
	{
		return $this->_session->{$this->_member};
	}
	
	 /**
	 * Defined by Zend_Auth_Storage_Interface
	 *
	 * @param  mixed $contents
	 * @return void
	 */
	public function write($contents)
	{
		$this->_session->{$this->_member} = $contents;
	}
	
	/**
	 * Defined by Zend_Auth_Storage_Interface
	 *
	 * @return void
	 */
	public function clear()
	{
		unset($this->_session->{$this->_member});
	}
	
}