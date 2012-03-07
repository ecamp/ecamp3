<?php

namespace Core\Validator\Entity;

class CampValidator extends \Core\Validator\Entity
{
	
	protected function init()
	{
		$this->get('start')
			->addValidator(new \Zend_Validate_Date())
			->setRequired(true);
		
		$this->get('duration')
			->addValidator(new \Zend_Validate_Int())
			->addValidator(new \Zend_Validate_GreaterThan(0))
			->setRequired(true);
		
	}
	
	
}