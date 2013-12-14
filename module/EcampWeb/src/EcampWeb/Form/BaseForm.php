<?php

namespace EcampWeb\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use EcampLib\Validation\ValidationException;

abstract class BaseForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        // general properties
        $this->setAttribute('method', 'post');

        $errorField = new Element\Hidden('formError');
        $this->add($errorField);
    }

    public function setFormError($errorMessage)
    {
        $this->setMessages(array('formError' => array($errorMessage)));
    }

    public function setAction($url)
    {
        $this->setAttribute('action', $url);
    }

    public function setRedirectAfterSuccess($url)
    {
        $this->setAttribute('data-redirect-after-success', $url);
    }

    public function extractFromException(ValidationException $ex)
    {
        $error = $ex->getMessageArray();
        var_dump($error);
        if ($error['data'] && is_array($error['data'])) {
            $this->setMessages($error['data']);
        } else {
            $this->setFormError($error);
        }
    }
}
