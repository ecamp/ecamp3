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

class QuickSwitchDropdown extends BaseForm
{
	private $manager;
	
	public function __construct($manager, $options = array())
	{
		$this->manager = $manager;
		
		parent::__construct($options);
	}
	
	public function init()
	{
		parent::standardDecorators();
		
		$dropdown = new \Zend_Form_Element_Select($this->manager->getForwardKeyword());
		$dropdown->addMultiOption('', 'QuickSwitch');
		
		foreach($this->manager->getLinks() as $link)
		{	$dropdown->addMultiOption($link->id, $link->entity->getName());	}
		
		// onChange="this.form.submit()"
		$dropdown->setAttrib('onChange', 'this.form.submit()');
		
		$submit = new \Zend_Form_Element_Submit('Go');
		
		
		$this->addElement($dropdown);
		//$this->addElement($submit);
		
		$this->setAction($this->manager->getControllerUrl());
	}
	
}
