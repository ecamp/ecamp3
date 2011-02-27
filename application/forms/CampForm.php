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

class Application_Form_CampForm extends Ztal_Form
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
		$campName->setLabel('Camp Name')
			->addFilter('StringTrim');


		$campSlogan = new Zend_Form_Element_Text('campSlogan');
		$campSlogan->setLabel('Camp Slogan')
			->addFilter('StringTrim')
			->setRequired(true);
	    
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');


		$this->addElement($id);
		$this->addElement($campName);
		$this->addElement($campSlogan);

		$this->addElement($submit);

        /* form based errors */
	    $this->clearDecorators();

		$this->addDecorator('FormErrors')
	    ->addDecorator('FormElements')
         ->addDecorator('HtmlTag')
         ->addDecorator('Form');

	    $this->setElementDecorators(array(
            array('ViewHelper'),
            array('Description'),
            array('Label', array('separator'=>' ')),
            array('HtmlTag', array('class'=>'element-group')),
        ));

	    // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array('Description'),
            array('HtmlTag', array('class'=>'submit-group')),
        ));

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


	public function getEditLink($campId)
	{
		return "/camp/index/" . $campId;
	}

}

