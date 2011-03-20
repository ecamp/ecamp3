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

		$start_validator = new Zend_Validate_Regex('/^[a-z0-9]/');
        $start_validator->setMessage('Value needs to start with a lower letter or a number.');

	    $name_validator = new Zend_Validate_Regex('/^[a-z0-9_-]+$/');
        $name_validator->setMessage('Value can only contain lower letters, numbers, underscores (_) and dashes (-).');

		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Name (unique)')
			->addFilter('StringTrim')
			->addFilter('StringToLower')
			->setRequired(true)
	        ->addValidator($name_validator)
			->addValidator($start_validator)
			->addValidator(new Zend_Validate_StringLength(array('min' => 5, 'max' => 20)));


		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Title')
			->addFilter('StringTrim')
			->setRequired(true);

	    $date_validator = new Zend_Validate_Date(array('format' => 'dd.mm.yyyy'));

	    $from = new ZendX_JQuery_Form_Element_DatePicker(
                    'from',
                    array(
	                    "label" => "From",
	                    'jQueryParams' => array('dateFormat' => 'dd.mm.yy')));
	    $from->setRequired(true)->addValidator($date_validator);

	    $to   = new ZendX_JQuery_Form_Element_DatePicker(
                    'to',
                    array(
	                    "label" => "To",
	                    'jQueryParams' => array('dateFormat' => 'dd.mm.yy')));
	    $to->setRequired(true)->addValidator($date_validator);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Create');

		$this->addElement($id);
		$this->addElement($name);
		$this->addElement($title);
		$this->addElement($from);
	    $this->addElement($to);
		$this->addElement($submit);

    }

	/*
	public function setData(Entity\Camp $camp)
	{
		$this->getElement('id')
				->setValue($camp->getId());
		
		$this->getElement('name')
				->setValue($camp->getName());

		$this->getElement('title')
				->setValue($camp->getTitle());

	}*/


	public function grabData(Entity\Camp $camp, Entity\Period $period)
	{
		$camp->setName($this->getValue('name'));

		$camp->setTitle($this->getValue('title'));

		$period->setStart(new DateTime($this->getValue('from')));

		$period->setDuration($this->getValue('to') - $this->getValue('from') + 1);
	}

	
	public function getId()
	{
		return $this->getValue('id');
	}

	public function isValid($data)
	{
		$s = parent::isValid($data);
		if( !$s )
			return false;

		if( $this->getValue("from") >  $this->getValue("to") ){
			$this->getElement("from")->addError("'From' date can not be larger than 'To' date.");
			return false;
		}

		return true;
	}

}

