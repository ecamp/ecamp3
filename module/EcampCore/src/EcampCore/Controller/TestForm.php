<?php

namespace EcampCore\Controller;

use EcampWeb\Form\BaseForm;

class TestForm extends \EcampCore\Form\BaseForm
{
    public function __construct()
    {
        parent::__construct('test');

    }

    public function init()
    {
        parent::init();

        $day = new \EcampCore\Fieldset\DayFieldset('day', array('show' => true));
        $day->init();

        $this->add($day);

        $this->add(array(
            'name' => 'input-text-success',
            'attributes' => array(
                'type' => 'text',
                'id' => 'inputSuccess'
            ),
            'options' => array(
                'label' => 'Input with success',
//                'validation-state' => 'success',
//                'feedback-icon' => 'ok',
                'column-size' => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2')
            )
        ))->add(array(
            'name' => 'input-text-warning',
            'attributes' => array(
                'type' => 'text',
                'id' => 'inputWarning'
            ),
            'options' => array(
                'label' => 'Input with warning',
//                'validation-state' => 'warning',
                'column-size' => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2')
            )
        ))->add(array(
            'name' => 'input-text-error',
            'attributes' => array(
                'type' => 'text',
                'id' => 'inputError'
            ),
            'options' => array(
                'label' => 'Input with error',
//                'validation-state' => 'error',
//                'feedback-icon' => 'remove',
                'column-size' => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2')
            )
        ))->add(array(
            'name' => 'select',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Select',
                'value_options' => array(
                    '0' => 'French',
                    '1' => 'English',
                    '2' => 'Japanese',
                    '3' => 'Chinese',
                ),
                'column-size' => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2')
            )
        ))->add(array(
                'name' => 'select',
                'type' => 'Zend\Form\Element\Radio',
                'options' => array(
                    'label' => 'Radio',
                    'value_options' => array(
                        '0' => 'French',
                        '1' => 'English',
                        '2' => 'Japanese',
                        '3' => 'Chinese',
                    ),
                    'column-size' => 'sm-10',
                    'label_attributes' => array('class' => 'col-sm-2')
                )
            ))
            ->add(array(
                'name' => 'checkbox',
                'type' => 'Zend\Form\Element\Checkbox',
                'options' => array(
                    'label' => 'Checkbox',
                    'column-size' => 'sm-10',
                    'label_attributes' => array('class' => 'col-sm-2')
                )
            ))
        ;

        $this->get('input-text-error')->setMessages(array('asdf' => 'asdfaslihlihsdf'));

    }

}
