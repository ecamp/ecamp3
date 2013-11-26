<?php

namespace EcampWeb\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use Zend\Form\Element;

abstract class BaseForm extends Form
{
    public function __construct($entityManager)
    {
        parent::__construct('period-form');

        // general properties
        $this->setAttribute('method', 'post');

        // The form will hydrate an object of type "period"
        $this->setHydrator(new DoctrineHydrator($entityManager));

        $errorField = new Element\Hidden('formError');
        $this->add($errorField);
    }

    public function setFormError($errorMessage)
    {
        $this->setMessages(array('formError' => array($errorMessage)));
    }
}
