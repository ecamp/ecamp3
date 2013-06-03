<?php

namespace EcampWeb\Form\User;

use Zend\Form\Form;
use Doctrine\ORM\EntityManager;

class BypassLoginForm
	extends Form
{
	
	public function __construct(EntityManager $orm){
		
		parent::__construct('BypassLoginForm');
		
		$this->setAttribute('method', 'get');
		
		$userSelect = $this->add(array(
		    'type' => 'DoctrineModule\Form\Element\ObjectSelect',
		    'name' => 'user',
		    'options' => array(
		        'object_manager' => $orm,
		        'target_class'   => 'EcampCore\Entity\User',
		        'property'       => 'displayName',
		        'is_method'      => true,
// 		        'find_method'    => array(
// 		            'name'   => 'findBy',
// 		            'params' => array(
// 		                'criteria' => array('active' => 1),
// 		                'orderBy'  => array('lastName' => 'ASC'),
// 		            ),
// 		        ),
		    ),
		));
		
		
		$this->add(array(
			'type' => 'DoctrineModule\Form\Element\ObjectRadio',
			'name' => 'user_radio',
			'options' => array(
				'object_manager' => $orm,
				'target_class'   => 'EcampCore\Entity\User',
				'property'       => 'displayName',
				'is_method'      => true,
// 		        'find_method'    => array(
// 		            'name'   => 'findBy',
// 		            'params' => array(
// 		                'criteria' => array('active' => 1),
// 		                'orderBy'  => array('lastName' => 'ASC'),
// 		            ),
// 		        ),
			),
		));
		
		$this->add(array(
			'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
			'name' => 'user_checkbox',
			'options' => array(
				'object_manager' => $orm,
				'target_class'   => 'EcampCore\Entity\User',
				'property'       => 'displayName',
				'is_method'      => true,
// 		        'find_method'    => array(
// 		            'name'   => 'findBy',
// 		            'params' => array(
// 		                'criteria' => array('active' => 1),
// 		                'orderBy'  => array('lastName' => 'ASC'),
// 		            ),
// 		        ),
			),
		));
		
		$this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Login',
            ),
        ));
		
	}
	
}