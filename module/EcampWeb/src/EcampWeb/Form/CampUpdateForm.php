<?php

namespace EcampWeb\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use EcampCore\Validation\CampFieldset;

class CampUpdateForm extends Form
{
	public function __construct($entityManager)
	{
		parent::__construct('camp-update-form');

		// general properties
		$this->setAttribute('method', 'post');
		
		// The form will hydrate an object of type "camp"
		$this->setHydrator(new DoctrineHydrator($entityManager,'EcampCore\Entity\Camp'));
		
		// add camp Fieldset
		$campFieldset = new CampFieldset($entityManager);
		$campFieldset->setUseAsBaseFieldset(true);
		$this->add($campFieldset);
		
		// … add CSRF and submit elements …
		$this->add(new \Zend\Form\Element\Csrf('security'));
		$this->add(array(
				'name' => 'send',
				'type'  => 'Submit',
				'attributes' => array(
						'value' => 'Submit',
				),
		));

	}
}