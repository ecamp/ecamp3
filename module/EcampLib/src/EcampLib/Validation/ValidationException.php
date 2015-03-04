<?php

namespace EcampLib\Validation;

use Zend\Form\Form;

class ValidationException extends \Exception
{
    private $validationMessages = array();

    public function __construct($validationMessages = array())
    {
        parent::__construct("User input validation failed: " . var_export($validationMessages, true));
        $this->setValidationMessages($validationMessages);
    }

    public static function Create(array $messages)
    {
        return new self($messages);
    }

    public static function ValueRequired($name)
    {
        return new self(array($name => "Value is required and can't be empty"));
    }

    public static function FromForm(Form $form)
    {
        return new self($form->getMessages());
    }

    public static function FromInnerException($path, ValidationException $innerException)
    {
        return new self(array($path => $innerException->getValidationMessages()));
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
