<?php

namespace CoreApi\Service;

class ValidationResponse
{
	
	private $isValid = false;
	
	private $messages = array();
	
	
	public function __construct($isValid = false)
	{
		$this->isValid = $isValid;
	}
	
	public function isValid()
	{
		return $this->isValid;
	}
	
	public function addMessage($message)
	{
		$this->messages[] = $message;
	}
	
	public function getMessages()
	{
		return $this->messages;
	}
	
}

