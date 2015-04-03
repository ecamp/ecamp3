<?php

namespace EcampCore\Fieldset\Camp;

use EcampLib\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class CampCreateFieldset extends BaseFieldset
    implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('camp-create');
    }

    public function init()
    {
        $this->add(array(
            'name' => 'campType',
            'type' => 'EcampCore\Entity\Camp.campType',
            'options' => array(
                'label' => 'Type',
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
    }

    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name' => 'name',
                'required' => true,
            ),
        );
    }
}
