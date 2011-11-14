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

class Application_Form_User extends Ztal_Form
{

    public function init()
    {

		$id = new Zend_Form_Element_Text('id');
		$id->setLabel('Id:')
			->setRequired(true);


		$scoutName = new Zend_Form_Element_Text('scoutName');
		$scoutName->setLabel('PfadiName:')
			->addFilter('StringTrim')
			->setRequired(true);


		$firstName = new Zend_Form_Element_Text('firstName');
		$firstName->setLabel('VorName:')
			->addFilter('StringTrim')
			->setRequired(true);


		$surName = new Zend_Form_Element_Text('surName');
		$surName->setLabel('NachName:')
			->addFilter('StringTrim')
			->setRequired(true);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');

		

		$this->addElement($id);
		$this->addElement($scoutName);
		$this->addElement($firstName);
		$this->addElement($surName);

		$this->addElement($submit);


    }


	public function setData(Entity\User $user)
	{
		$this->getElement('id')
				->setValue($user->getId());
		
		$this->getElement('scoutName')
				->setValue($user->getScoutname());

		$this->getElement('firstName')
				->setValue($user->getFirstname());

		$this->getElement('surName')
				->setValue($user->getSurname());
	}


	public function grabData(Entity\User $user)
	{
		$user->setScoutname($this->getValue('scoutName'));

		$user->setFirstname($this->getValue('firstName'));

		$user->setSurname($this->getValue('surName'));
	}

	
	public function getId()
	{
		return $this->getValue('id');
	}


	public function getEditLink($userId)
	{
		return "/doctrine/index/" . $userId;
	}

}

