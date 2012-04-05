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

class GroupRequest extends BaseForm
{
	public function init()
	{
		parent::standardDecorators();
		
		$name = new \Zend_Form_Element_Text('name');
		$name->setLabel('Name (unique)')
			->addFilter('StringTrim')
			->addFilter('StringToLower');

		$description = new \Zend_Form_Element_Text('description');
		$description->setLabel('Description')
			->addFilter('StringTrim');

		$motivation = new \Zend_Form_Element_Textarea('motivation');
		$motivation->setLabel('Motivation')
			->addFilter('StringTrim');

		$submit = new \Zend_Form_Element_Submit('submit');
		$submit->setLabel('Create');
		
		$this->addElement($name);
		$this->addElement($description);
		$this->addElement($motivation);
		$this->addElement($submit);
		
		$this->setAction('savegrouprequest');
	}
	
}
