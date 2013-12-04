<?php
namespace EcampCore\Validation;

use Zend\Validator\Callback;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class PeriodFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct($entityManager = null)
    {
        parent::__construct('period');

        // The form will hydrate an object of type "period"
        if( $entityManager )
            $this->setHydrator(new DoctrineHydrator($entityManager));

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
            )
        );
    }
}
