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

namespace CoreApi\Service\Params;

use CoreApi\Service\Params\Params;


class ArrayParams extends Params{
	
	private $values = array();
	private $messages = array();
	private $errors = array();
	
	public function getValue($name){
		$this->assertKeyExists($name);
		return $this->values[$name];
	}
	
	public function setValue($name, $value){
		$this->values[$name] = $value;
		$this->messages[$name] = array();
		$this->errors[$name] = array();
	}
	
	public function hasElement($name){
		return array_key_exists($name, $this->values);
	}
	
	
	
	public function addMessage($name, $message){
		$this->assertKeyExists($name);
		$this->messages[$name][] + $message;
	}
	
	public function addMessages($name, array $messages){
		$this->assertKeyExists($name);
		$this->messages[$name] = array_merge($this->messages[$name], $messages);
	}
	
	public function setMessages($name, array $messages){
		$this->assertKeyExists($name);
		$this->messages[$name] = $messages;
	}
	
	public function getMessages($name){
		$this->assertKeyExists($name);
		return $this->messages[$name];
	}
	
	public function clearMessages($name){
		$this->assertKeyExists($name);
		$this->messages[$name] = array();
	}
	
	
	
	public function addError($name, $error){
		$this->assertKeyExists($name);
		$this->errors[$name][] = $error;
	}
	
	public function addErrors($name, array $errors){
		$this->assertKeyExists($name);
		$this->errors[$name] = array_merge($this->errors[$name], $errors);
	}
	
	public function setErrors($name, array $errors){
		$this->assertKeyExists($name);
		$this->errors[$name] = $errors;
	}
	
	public function getErrors($name){
		$this->assertKeyExists($name);
		return $this->errors[$name];
	}
	public function clearErrors($name){
		$this->assertKeyExists($name);
		$this->errors[$name] = array();
	}
	
	
	private function assertKeyExists($name){
		if(! $this->hasElement($name)){
			throw new \Exception("No Entry for $name available");
		}
	}
} 
