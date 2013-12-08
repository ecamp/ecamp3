<?php

namespace EcampCore\Validation\Period;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class PeriodSizeFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('period-size');

        $this->add(array(
            'name' => 'start',
            'options' => array(
                'label' => 'Start date',
            ),
            'type' => 'Date',
        ));

        $this->add(array(
            'name' => 'end',
            'options' => array(
                'label' => 'End date',
            ),
            'type' => 'Date',
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name' => 'end',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'date',
                    ),
                ),
            ),
            array(
                'name' => 'start',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'date',
                    ),
                ),
            )
        );
    }
}
