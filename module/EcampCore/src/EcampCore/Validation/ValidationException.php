<?php

namespace EcampCore\Validation;

use EcampWeb\Form\BaseForm;

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

    public function pushToForm(BaseForm $form)
    {
        $error = $this->getMessageArray();

        if ($error['data'] && is_array($error['data'])) {
            $form->setMessages($error['data']);
        } else {
            $form->setFormError($error);
        }
    }

}
