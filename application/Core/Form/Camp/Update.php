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

namespace Core\Form\Camp;

/**
 * Validation form to create/update camps
 * 
 */
class Update extends \Core\Form\BaseForm
{
	
	public function init()
	{
		$id = new \Zend_Form_Element_Text('id');

		$name_validator = new \Zend_Validate_Regex('/^[a-z0-9][a-z0-9_-]+$/');
		$name_validator->setMessage('Value can only contain lower letters, numbers, underscores (_) and dashes (-) and needs to start with a letter or number.');

		$name = new \Zend_Form_Element_Text('name');
		$name->setRequired(true)
			->addValidator($name_validator)
			->addValidator(new \Zend_Validate_StringLength(array('min' => 5, 'max' => 20)));


		$title = new \Zend_Form_Element_Text('title');
		$title->setRequired(true);

		$date_validator = new \Zend_Validate_Date(array('format' => 'dd.mm.yyyy'));

		$this->addElement($id);
		$this->addElement($name);
		$this->addElement($title);
	}

	public function getData(\Core\Entity\Camp $camp)
	{
		$camp->setName($this->getValue('name'));

		$camp->setTitle($this->getValue('title'));
	}

	public function getId()
	{
		return $this->getValue('id');
	}
}
