<?php

namespace EcampWeb\Form;

use Zend\Form\Form;
use Zend\Form\Element;

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
}
