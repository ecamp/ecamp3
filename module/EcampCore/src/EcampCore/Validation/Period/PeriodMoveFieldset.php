<?php

namespace EcampCore\Validation\Period;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class PeriodMoveFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('period-move');

        $this->add(array(
            'name' => 'start',
            'options' => array(
                'label' => 'Start date',
            ),
            'type' => 'Date',
        ));

        $this->add(array(
            'name' => 'periodOnly',
            'options' => array(
                'label' => 'Keep days at date'
            ),
            'type' => 'Checkbox'
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name' => 'start',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'date',
                    ),
                ),
            ),
        );
    }
}
