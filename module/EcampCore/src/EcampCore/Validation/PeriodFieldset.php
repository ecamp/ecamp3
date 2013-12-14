<?php
namespace EcampCore\Validation;

use Zend\Validator\Callback;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class PeriodFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('period');

        $this->add(array(
            'name' => 'start',
            'options' => array(
                'label' => 'Start date',
            ),
            'type'  => 'Date',
        ));

        $this->add(array(
                'name' => 'end',
                'options' => array(
                        'label' => 'End date',
                ),
                'type'  => 'Date',
        ));

        $this->add(array(
                'name' => 'description',
                'options' => array(
                        'label' => 'Description'
                ),
                'type'  => 'Text'
        ));

        $this->add(array(
            'name' => 'moveEvents',
            'options' => array(
                'label' => 'Move events with period'
            ),
            'attributes' => array(
                'checked' => 'checked',
            ),
            'type' => 'Checkbox'
        ));

    }

    /**
     * @return array
     */
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

            array(
                'name' => 'end',
                'required' => true,
                'validators' => array(
                        array(
                                'name' => 'date',
                        ),

                        array(
                                'name'     => 'Callback',
                                'options' => array(
                                        'message' => array(
                                                Callback::INVALID_VALUE => 'Minimum duration of period is 1 day.',
                                        ),
                                        'callback' => function($value, $context=array()) {
                                            return $value >= $context['start'];
                                        },
                                ),
                        )
                ),
            ),
            array(
                'name' => 'moveEvents',
                'required' => false,
            )
        );
    }
}
