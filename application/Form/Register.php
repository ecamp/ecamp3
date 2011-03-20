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

namespace Form;

class Register extends \Ztal_Form
{
	public function init()
    {

		$userName = new \Zend_Form_Element_Text('username');
		$userName->setLabel('Username:')
			->setRequired(true);



		$mail = new \Zend_Form_Element_Text('mail');
		$mail->setLabel('Mail:')
			->addValidator('EmailAddress')
			->setRequired(true);


		$scoutName = new \Zend_Form_Element_Text('scoutname');
		$scoutName->setLabel('Scoutname:')
			->addFilter('StringTrim')
			->setRequired(false);


		$firstName = new \Zend_Form_Element_Text('firstname');
		$firstName->setLabel('Firstname:')
			->addFilter('StringTrim')
			->setRequired(false);


		$surName = new \Zend_Form_Element_Text('surname');
		$surName->setLabel('Surname:')
			->addFilter('StringTrim')
			->setRequired(false);

		$submit = new \Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');



		$this->addElement($userName);
		$this->addElement($mail);
		$this->addElement($scoutName);
		$this->addElement($firstName);
		$this->addElement($surName);

		$this->addElement($submit);

		$this->setAction('/register/register');

    }

}
