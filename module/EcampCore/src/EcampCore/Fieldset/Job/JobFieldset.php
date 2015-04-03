<?php

namespace EcampCore\Fieldset\Job;

use EcampLib\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class JobFieldset extends BaseFieldset
    implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('job');
    }

    public function init()
    {
        $this->add(array(
            'name' => 'name',
            'type' => 'EcampCore\Entity\Job.name',
            'options' => array(
                'label' => 'Name',
                'column-size' =>  'sm-9',
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
                'filters' => array(
                    array('name' => 'EcampCore\Entity\Job.name')
                ),
                'validators' => array(
                    array('name' => 'EcampCore\Entity\Job.name')
                )
            ),
        );
    }
}
