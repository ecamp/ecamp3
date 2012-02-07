<?php

namespace Core\Validator;

class Element extends \Zend_Validate
{
	private $isRequired = false;
	
	public function setRequired($isRequired = true)
	{
		$this->isRequired = $isRequired;
	}
	
	public function isValid($value)
	{
		$result = parent::isValid($value);
	
		if($this->isRequired)
		{
			$notEmpty = new \Zend_Validate_NotEmpty();
			
			if (! $notEmpty->isValid($value))
			{
				$result = false;
				$messages = $notEmpty->getMessages();
				$this->_messages = array_merge($this->_messages, $messages);
			}
		}
		
		return $result;
	}
}