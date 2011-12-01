<?php

namespace Core\Validate;

class LoginValidator extends Entity
{
	
	protected function init()
	{
		$this->get('password')
			->addValidator(new \Zend_Validate_StringLength(array('min' => 6)));
	}
	
	
}