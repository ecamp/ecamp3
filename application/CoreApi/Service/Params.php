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

namespace CoreApi\Service;

use CoreApi\Service\Params\FormParams;
use CoreApi\Service\Params\ArrayParams;

abstract class Params {

	public static function Create($target){
		if($target instanceof \Zend_Form)
			return self::FromForm($target);
		
		if(is_array($target))
			return self::FromArray($target);
		
		throw new InvalidArgumentException(
			"Given Target can not be used to create a Params-Object!");
	}
	
	public static function FromForm(\Zend_Form $form){
		return new FormParams($form);
	}
	
	public static function FromArray(array $array){
		return new ArrayParams($array);
	}
	
	
	public function __get($name){
		return $this->getValue($name);
	}
	
	public function __set($name, $value){
		$this->setValue($name, $value);
	}
	
	public function __isset($name){
		$this->hasElement($name);
	}
	
	
	public abstract function getValue($name);
	public abstract function setValue($name, $value);
	public abstract function hasElement($name);
	
	public abstract function addMessage($name, $message);
	public abstract function addMessages($name, array $messages);
	public abstract function setMessages($name, array $messages);
	public abstract function getMessages($name);
	public abstract function clearMessages($name);
	
	public abstract function addError($name, $error);
	public abstract function addErrors($name, array $errors);
	public abstract function setErrors($name, array $errors);
	public abstract function getErrors($name);
	public abstract function clearErrors($name);

}