<?php

namespace EcampCore\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;
use EcampCore\Entity\Camp;

use EcampCore\Filter\CampFilter;

class CampUpdateForm extends Form
{
	public function __construct(ServiceManager $serviceManager)
	{
		parent::__construct('camp-update-form');
		$entityManager = $serviceManager->get('Doctrine\ORM\EntityManager');

		// The form will hydrate an object of type "camp"
		$this->setHydrator(new DoctrineHydrator($entityManager,'EcampCore\Entity\Camp'));

		// … add elements
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
		
		// … add CSRF and submit elements …
		$this->add(array(
				'type' => '\Zend\Form\Element\Captcha',
				'name' => 'captcha',
				'options' => array(
						'label' => 'Please verify you are human.',
						'captcha' => $this->captcha,
				),
		));
		$this->add(new \Zend\Form\Element\Csrf('security'));
		$this->add(array(
				'name' => 'send',
				'type'  => 'Submit',
				'attributes' => array(
						'value' => 'Submit',
				),
		));

	
		// Optionally set your validation group here
		$this->setInputFilter(new CampFilter());
	}
}