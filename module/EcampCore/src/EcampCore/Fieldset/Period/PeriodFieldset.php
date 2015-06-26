<?php

namespace EcampCore\Fieldset\Period;

use EcampLib\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class PeriodFieldset extends BaseFieldset
    implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('period');
    }

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
        $this->add(array(
            'name' => 'description',
            'type'  => 'EcampCore\Entity\Period.description',
            'options' => array(
                'label' => 'Description',
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

        );
    }
}
