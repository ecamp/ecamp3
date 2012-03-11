<?php

namespace CoreApi\Service;


class ValidationException extends \Exception
{
	private $messages = array();
	
	public function addMessage($key, $message)
	{
		$this->messages[$key] = $message;
	}
	
	public function getMessages()
	{
		return $this->messages;
	}
	
}