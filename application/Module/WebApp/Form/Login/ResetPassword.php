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

namespace WebApp\Form\Login;

class ResetPassword extends \Ztal_Form
{

	public function init()
    {
		$resetKey = new \Zend_Form_Element_Hidden('resetKey');
		$resetKey->setRequired(true);

		
		$password1 = new \Zend_Form_Element_Password('password1');
		$password1->setLabel('Password:')
			
			// TODO: Move this validation to \Core\Validate\LoginValidator when the 
			// 		 new validation concept is accepted.
			->addValidator(new \Zend_Validate_StringLength(array('min' => 6)))

		->setRequired(true);
		
		$password2 = new \Zend_Form_Element_Password('password2');
		$password2->setLabel('Repeat Password:')
			
			// TODO: Move this validation to \Core\Validate\LoginValidator when the 
			//		 new validation concept is accepted.
			->addValidator(new \Zend_Validate_Identical('password1'))

			->setRequired(true);
		
		$submit = new \Zend_Form_Element_Submit('submit');
		$submit->setLabel('Submit');


		$this->addElement($resetKey);
		$this->addElement($password1);
		$this->addElement($password2);
		$this->addElement($submit);
	}
}
