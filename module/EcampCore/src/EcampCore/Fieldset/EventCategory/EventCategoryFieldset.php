<?php

namespace EcampCore\Fieldset\EventCategory;

use EcampLib\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class EventCategoryFieldset extends BaseFieldset
    implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('event-category');
    }

    public function init()
    {
        $this->add(array(
            'name' => 'name',
            'type' => 'EcampCore\Entity\EventCategory.name',
            'options' => array(
                'label' => 'Name',
                'column-size' =>  'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                )
            )
        ));

        $this->add(array(
            'name' => 'short',
            'type' => 'EcampCore\Entity\EventCategory.short',
            'options' => array(
                'label' => 'Short',
                'column-size' => 'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                )
            )
        ));

        $this->add(array(
            'name' => 'eventType',
            'type' => 'EcampCore\Entity\EventCategory.eventType',
            'options' => array(
                'label' => 'Type',
                'column-size' => 'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                ),
                'property' => 'name'
            )
        ));

        $this->add(array(
            'name' => 'numberingStyle',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Numbering',
                'column-size' => 'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                ),
                'value_options' => array(
                    '1' => '1) 2) 3) Numerical',
                    'a' => 'a) b) c) Alphabetical lower case',
                    'A' => 'A) B) C) Alphabetical upper case',
                    'i' => 'i) ii) iii) Roman lower case',
                    'I' => 'I) II) III) Roman upper case'
                ),
            ),
            'attributes' => array(
                'class' => 'selectpicker',
                'data-style' => 'btn-default form-control'
            )
        ));

        $this->add(array(
            'name' => 'color',
            'type' => 'Zend\Form\Element\Color',
            'options' => array(
                'label' => 'Color',
                'column-size' => 'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                )
            )
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name' => 'name',
                'filters' => array(
                    array('name' => 'EcampCore\Entity\EventCategory.name')
                ),
                'validators' => array(
                    array('name' => 'EcampCore\Entity\EventCategory.name')
                )
            ),
            array(
                'name' => 'short',
                'filters' => array(
                    array('name' => 'EcampCore\Entity\EventCategory.short')
                ),
                'validators' => array(
                    array('name' => 'EcampCore\Entity\EventCategory.short')
                )
            ),
        );
    }
}
