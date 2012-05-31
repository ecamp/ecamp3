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

class EditProfile extends BaseForm
{
	public function init()
	{
		$username = new \Zend_Form_Element_Text('username');
		$username->setLabel('Username (unique)')
			->addFilter('StringTrim')
			->addFilter('StringToLower');

		$scoutname = new \Zend_Form_Element_Text('scoutname');
		$scoutname->setLabel('Scoutname')
			->addFilter('StringTrim')
			->addFilter('StringToLower');
		
		$firstname = new \Zend_Form_Element_Text('firstname');
		$firstname->setLabel('Firstname')
			->addFilter('StringTrim')
			->addFilter('StringToLower');
			
		$surname = new \Zend_Form_Element_Text('surname');
		$surname->setLabel('Surname')
			->addFilter('StringTrim')
			->addFilter('StringToLower');
			
		$street = new \Zend_Form_Element_Text('street');
		$street->setLabel('Street')
			->addFilter('StringTrim')
			->addFilter('StringToLower');
			
		$zipcode = new \Zend_Form_Element_Text('zipcode');
		$zipcode->setLabel('Zipcode')
			->addFilter('StringTrim')
			->addFilter('StringToLower');
			
		$city = new \Zend_Form_Element_Text('city');
		$city->setLabel('City')
			->addFilter('StringTrim')
			->addFilter('StringToLower');
			
		$homeNr = new \Zend_Form_Element_Text('homeNr');
		$homeNr->setLabel('Home Number')
			->addFilter('StringTrim')
			->addFilter('StringToLower');
			
		$mobilNr = new \Zend_Form_Element_Text('mobilNr');
		$mobilNr->setLabel('Mobil Number')
			->addFilter('StringTrim')
			->addFilter('StringToLower');

		$submit = new \Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');

		
		$this->addElement($username);
		$this->addElement($scoutname);
		$this->addElement($firstname);
		$this->addElement($surname);
		$this->addElement($street);
		$this->addElement($zipcode);
		$this->addElement($city);
		$this->addElement($homeNr);
		$this->addElement($mobilNr);
		
		$this->addElement($submit);
		
		$this->setAction('updateprofile');

	}
	
}