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

namespace WebApp\Form;

class Login extends \Ztal_Form
{

	public function init()
    {
		$login = new \Zend_Form_Element_Text('login');
		$login->setLabel('Login:');

		$password = new \Zend_Form_Element_Password('password');
		$password->setLabel('Password:');

		$submit = new \Zend_Form_Element_Submit('submit');
		$submit->setLabel('Login');


		$this->addElement($login);
		$this->addElement($password);
		$this->addElement($submit);
	}
}
