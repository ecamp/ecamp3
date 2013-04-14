<?php

namespace Core\Validator\Entity;

class UserValidator extends \Core\Validator\Entity
{
	
	protected function init()
	{	
		$this->get('scoutname')
			->setRequired(false);
		
		$this->get('firstname')
			->addValidator(new \Zend_Validate_StringLength(array('min' => 2)))
			->setRequired(true);
		
		
		$this->get('surname')
			->setRequired(true);
	}
	
	
}