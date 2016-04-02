<?php

namespace EcampLib\Form;

use Zend\Form\Fieldset;

abstract class BaseFieldset extends Fieldset
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setAttribute('style', 'margin: 0 15px');
    }

}
