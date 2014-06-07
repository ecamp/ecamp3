<?php

namespace EcampWeb\Form\Period;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class PeriodStartEndFieldset extends Fieldset
    implements InputFilterProviderInterface
{

    public function init()
    {
        $this->add(array(
            'name' => 'start',
            'type' => 'EcampCore\Entity\Period.start',
            'options' => array(
                'label' => 'Start',
                'column-size' => 'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                )
            )
        ));
        $this->add(array(
            'name' => 'end',
            'type' => 'Date',
            'options' => array(
                'label' => 'End',
                'column-size' => 'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                ),
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name' => 'start',
                'filters' => array(
                    array('name' => 'EcampCore\Entity\Period.start')
                ),
                'validators' => array(
                    array('name' => 'EcampCore\Entity\Period.start')
                )
            ),

            array(
                'name' => 'end',
                'filters' => array(
                ),
                'validators' => array(
                    array('name' => 'Date')
                )
            ),
        );
    }
}
