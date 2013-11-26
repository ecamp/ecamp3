<?php

namespace EcampCore\Validation;

class ValidationException extends \Exception
{
    private $errorArray = null;

    public function __construct($errorMessage=null, $errorArray=null)
    {
        parent::__construct($errorMessage);
        $this->setMessageArray($errorArray);
    }

    public function setMessageArray($errorArray)
    {
        $this->errorArray = $errorArray;
    }

    public function getMessageArray()
    {
        return $this->errorArray;
    }

}
