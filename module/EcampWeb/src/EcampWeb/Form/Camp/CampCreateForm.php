<?php

namespace EcampWeb\Form\Camp;

use EcampWeb\Form\AjaxBaseForm;
use Zend\InputFilter\InputFilterProviderInterface;

class CampCreateForm extends AjaxBaseForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('camp-create');
    }

    public function init()
    {
        $this->add(array(
            'name' => 'name',
            'type' => 'EcampCore\Entity\Camp.name',
            'options' => array(
                'label' => 'Name',
                'column-size' => 'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                )
            )
        ));
        $this->add(array(
            'name' => 'title',
            'type' => 'EcampCore\Entity\Camp.title',
            'options' => array(
                'label' => 'Title',
                'column-size' => 'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                )
            )
        ));
        $this->add(array(
            'name' => 'motto',
            'type' => 'EcampCore\Entity\Camp.motto',
            'options' => array(
                'label' => 'Motto',
                'column-size' => 'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                )
            )
        ));
        $this->add(array(
            'name' => 'campType',
            'type' => 'EcampCore\Entity\Camp.campType',
            'options' => array(
                'label' => 'CampType',
                'column-size' => 'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                )
            ),
            'attributes' => array(
                'class' => 'selectpicker',
                'data-style' => 'btn-default form-control'
            ),
        ));
        $this->add(array(
            'name' => 'owner',
            'type' => 'EcampCore\Entity\Camp.owner',
            'options' => array(
                'label' => 'Owner',
                'column-size' => 'sm-9',
                'label_attributes' => array(
                    'class' => 'col-sm-3'
                )
            ),
            'attributes' => array(
                'class' => 'selectpicker',
                'data-style' => 'btn-default form-control'
            ),
        ));

        $this->add(array(
            'name' => 'period',
            'type' => 'EcampWeb\Form\Period\PeriodStartEndFieldset'
        ));

        $this->add(new \Zend\Form\Element\Csrf('security'));

    }

    public function getInputFilterSpecification()
    {
        $this->

        return array(
            array(
                'name' => 'name',
                'filters' => array(
                    array('name' => 'EcampCore\Entity\Camp.name')
                ),
                'validators' => array(
                    array('name' => 'EcampCore\Entity\Camp.name')
                )
            ),
            array(
                'name' => 'title',
                'filters' => array(
                    array('name' => 'EcampCore\Entity\Camp.title')
                ),
                'validators' => array(
                    array('name' => 'EcampCore\Entity\Camp.title')
                )
            ),
            array(
                'name' => 'motto',
                'filters' => array(
                    array('name' => 'EcampCore\Entity\Camp.motto')
                ),
                'validators' => array(
                    array('name' => 'EcampCore\Entity\Camp.motto')
                )
            ),
            array(
                'name' => 'campType',
                'filters' => array(
                    array('name' => 'EcampCore\Entity\Camp.campType')
                ),
                'validators' => array(
                    array('name' => 'EcampCore\Entity\Camp.campType')
                )
            ),
            array(
                'name' => 'owner',
                'filters' => array(
                    array('name' => 'EcampCore\Entity\Camp.owner')
                ),
                'validators' => array(
                    array('name' => 'EcampCore\Entity\Camp.owner')
                )
            ),

        );
    }
}
