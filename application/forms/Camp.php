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

class Application_Form_Camp extends Application_Form_BaseForm
{
	/* define the attributes that are writable through this form */
	public $attributes = array('name', 'slogan');

	/* initialize elements */
    public function init()
    {
		$id = new Zend_Form_Element_Hidden('id');

		$campName = new Zend_Form_Element_Text('name');
		$campName->setLabel('Camp Name')
			->addFilter('StringTrim')
			->addFilter('StringtoLower')
			->setRequired(true);

		$campSlogan = new Zend_Form_Element_Text('slogan');
		$campSlogan->setLabel('Camp Slogan')
			->addFilter('StringTrim')
			->setRequired(true);
	    
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');

		$this->addElement($id);
		$this->addElement($campName);
		$this->addElement($campSlogan);
		$this->addElement($submit);
		
		$this->standardDecorators();
		
		// buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array('Description'),
            array('HtmlTag', array('class'=>'submit-group')),
        ));
    }

}

