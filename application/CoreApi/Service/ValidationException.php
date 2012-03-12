<?php

namespace CoreApi\Service;


class ValidationException extends \Exception
{
	private $messages = array();
	
	public function addMessage($message)
	{
		$this->messages[] = $message;
	}
	
	public function getMessages()
	{
		return $this->messages;
	}
	
}