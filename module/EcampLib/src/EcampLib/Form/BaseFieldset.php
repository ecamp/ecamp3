<?php

namespace EcampLib\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

abstract class BaseFieldset
    extends Fieldset
    implements IBaseFieldset, InputFilterProviderInterface
{

    public function getInputFilterSpecification()
    {
        return static::createInputFilterSpecification();
    }

}
