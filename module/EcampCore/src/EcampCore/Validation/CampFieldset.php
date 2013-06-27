<?php
namespace EcampCore\Validation;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CampFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function __construct($entityManager)
	{
		parent::__construct('camp');
		
		// The form will hydrate an object of type "camp"
		$this->setHydrator(new DoctrineHydrator($entityManager,'EcampCore\Entity\Camp'));
			 
		$this->add(array(
				'name' => 'name',
				'options' => array(
						'label' => 'Camp name',
				),
				'type'  => 'Text',
		));
		
		$this->add(array(
				'name' => 'title',
				'options' => array(
						'label' => 'Camp title',
				),
				'type'  => 'Text',
		));
		
	}
	
	/**
	 * @return array
	 */
	public function getInputFilterSpecification()
	{
		return array(
			array(
			    'name' => 'name',
			    'required' => true,
			    'validators' => array(
			        array(
			            'name' => 'not_empty',
			        ),
			        array(
			            'name' => 'string_length',
			            'options' => array(
			                'min' => 5
			            ),
			        ),
			    ),
			),
			
			array(
				'name' => 'title',
				'required' => true,
				'validators' => array(
						array(
								'name' => 'not_empty',
						),
						array(
								'name' => 'string_length',
								'options' => array(
										'min' => 5
								),
						),
				),
			)
		);
	}
}