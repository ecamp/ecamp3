<?php

namespace EcampLib\Validation;

class ValidationException extends \Exception
{
    private $validationMessages = array();

    public function __construct($validationMessages = array())
    {
        parent::__construct("User input validation failed");
        $this->setValidationMessages($validationMessages);
    }

    public function setValidationMessages(array $validationMessages)
    {
        $this->validationMessages = $validationMessages;
    }

    public function addValidationMessages(array $validationMessages)
    {
        $this->validationMessages =
            array_merge_recursive($this->validationMessages, $validationMessages);
    }

    public function getValidationMessages()
    {
        return $this->validationMessages;
    }

}
