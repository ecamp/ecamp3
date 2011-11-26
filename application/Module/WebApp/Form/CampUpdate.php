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

class CampUpdate extends BaseForm
{
	public function init()
	{
		$id = new \Zend_Form_Element_Hidden('id');

		$name = new \Zend_Form_Element_Text('name');
		$name->setLabel('Name (unique)')
			->addFilter('StringTrim')
			->addFilter('StringToLower');

		$title = new \Zend_Form_Element_Text('title');
		$title->setLabel('Title')
			->addFilter('StringTrim');

		$submit = new \Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');

		$this->addElement($id);
		$this->addElement($name);
		$this->addElement($title);

		$this->addElement($submit);

	}
	
}
