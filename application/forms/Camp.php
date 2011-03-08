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

class Application_Form_Camp extends Ztal_Form
{

	/**
	 * @var
	 * @Inject CampRepository
	 */
	private $campRepo;


    public function init()
    {

		$id = new Zend_Form_Element_Hidden('id');


		$campName = new Zend_Form_Element_Text('campName');
		$campName->setLabel('CampName:')
			->addFilter('StringTrim')
			->setRequired(true);


		$campSlogan = new Zend_Form_Element_Text('campSlogan');
		$campSlogan->setLabel('LagerThema:')
			->addFilter('StringTrim')
			->setRequired(false);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');


		$this->addElement($id);
		$this->addElement($campName);
		$this->addElement($campSlogan);

		$this->addElement($submit);


    }


	public function setData(Entity\Camp $camp)
	{
		$this->getElement('id')
				->setValue($camp->getId());
		
		$this->getElement('campName')
				->setValue($camp->getName());

		$this->getElement('campSlogan')
				->setValue($camp->getSlogan());

	}


	public function grabData(Entity\Camp $camp)
	{
		$camp->setName($this->getValue('campName'));

		$camp->setSlogan($this->getValue('campSlogan'));
	}

	
	public function getId()
	{
		return $this->getValue('id');
	}

}

