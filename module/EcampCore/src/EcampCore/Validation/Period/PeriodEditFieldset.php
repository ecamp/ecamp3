<?php

namespace EcampCore\Validation\Period;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class PeriodEditFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('period-edit');

        $this->add(array(
            'name' => 'description',
            'options' => array(
                'label' => 'Description',
            ),
            'type'  => 'Text'
        ));
    }

    public function getInputFilterSpecification()
    {
        return array();
    }
}
