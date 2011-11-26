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

namespace WebApp\Form;
/**
 * Base class for frontend forms
 *
 */
abstract class BaseForm extends \Ztal_Form
{
	/**
	 * Copy errors from one form to another (for matching element names only)
	 * @param \Zend_Form $form
	 */
	public function copyErrors( \Zend_Form $form ){
		$errors = $form->getMessages();
		foreach( $errors as $key => $value ){
			$element = $this->getElement($key);
			if( $element != null ){
				$element->addErrors($value);
				
			}
		}
	}
	
	/**
	* Load data from entity and set the form values
	* @param  $entity
	*/
	public function setData($entity)
	{
		foreach($this->getValues() as $key => $value)
		{
			$this->getElement($key)->setValue($entity->{'get'.ucfirst($key)}());
		}
	}

	/**
	 * Configure standard decorators for form
	 */
	public function standardDecorators()
	{
		$this->clearDecorators();
	
		$this->addDecorator('FormErrors')
		->addDecorator('FormElements')
		->addDecorator('HtmlTag')
		->addDecorator('Form');
	
		$this->setElementDecorators(array(
		array('ViewHelper'),
		array('Description'),
		array('Label', array('separator'=>' ')),
		array('HtmlTag', array('class'=>'element-group')),
		));
	}
}

