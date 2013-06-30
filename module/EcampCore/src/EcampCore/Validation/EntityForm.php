<?php

namespace EcampCore\Validation;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;
use EcampCore\Validation\ValidationException;

class EntityForm extends Form
{
	public function __construct($entityManager, $fieldset, $entity)
	{
		parent::__construct('entity-form');
		
		$fieldset->useAsBaseFieldset(true);
		$fieldset->setObject($entity);
		
		$this->add($fieldset);
		$this->setHydrator(new DoctrineHydrator($entityManager));
		$this->bind($entity);
	}
	
	public function setDataAndValidate($data)
	{
		$this->setData($data);
		
		if( !$this->isValid() ){
			throw new ValidationException(json_encode($this->getMessages()));
		}
	}
}