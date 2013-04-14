<?php

namespace Core\Validator\Entity;

class CampValidator extends \Core\Validator\Entity
{
	
	protected function init()
	{
		$name_validator = new \Zend_Validate_Regex('/^[a-z0-9][a-z0-9_-]+$/');
		$name_validator->setMessage('Value can only contain lower letters, numbers, underscores (_) and dashes (-) and needs to start with a letter or number.');
		
		$this->get('name')
			->addValidator(new \Zend_Validate_StringLength(array('min' => 5, 'max' => 20)))
			->addValidator($name_validator)
			->setRequired(true);
		
		$this->get('title')
			->setRequired(true);
		
	}
	
	
}