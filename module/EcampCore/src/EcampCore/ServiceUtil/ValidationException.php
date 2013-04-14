<?php

namespace EcampCore\ServiceUtil;


class ValidationException extends \Exception
{
	private $messages = array();
	
	public function addMessage($message)
	{
		$this->messages[] = $message;
		$this->message = implode("; ", $this->messages);
	}
	
	public function getMessages()
	{
		return $this->messages;
	}
	
}