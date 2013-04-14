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

namespace EcampCore\Service\Params;

use EcampCore\Service\Params\Params;


class FormParams extends Params{
	
	private $form;
	
	public function __construct(\Zend_Form $form){
		$this->form = $form;
	}
	
	public function getValue($name){
		return $this->form->getElement($name)->getValue($name);
	}
	
	public function setValue($name, $value){
		$this->form->getElement($name)->setValue($value);
	}
	
	public function hasElement($name){
		return $this->form->getElement($name) != null;
	}
	
	
	
	public function addMessage($name, $message){
		$this->form->getElement($name)->addErrorMessage($message);
	}
	
	public function addMessages($name, array $messages){
		$this->form->getElement($name)->addErrorMessages($messages);
	}
	
	public function setMessages($name, array $messages){
		$this->form->getElement($name)->setErrorMessages($messages);
	}
	
	public function getMessages($name){
		$this->form->getElement($name)->getErrorMessages();
	}
	
	public  function clearMessages($name){
		$this->form->getElement($name)->clearErrorMessages();
	}
	
	
	
	public function addError($name, $message){
		$this->form->getElement($name)->addError($message);
	}
	
	public function addErrors($name, array $messages){
		$this->form->getElement($name)->addErrors($messages);
	}
	
	public function setErrors($name, array $messages){
		$this->form->getElement($name)->setErrors($messages);
	}
	
	public function getErrors($name){
		$this->form->getElement($name)->getErrors();
	}
	
	public function clearErrors($name){
		$this->form->getElement($name)->clearErrorMessages();
	}
	
} 
