<?php

namespace Core\Validator;

use CoreApi\Service\Params\Params;

abstract class Entity
{
	
	/**
	 * @var CoreApi\Entity\BaseEntity
	 */
	private $entity;
	
	
	/**
	 * @var array
	 */
	private $elements = array();
	
	
	
	
	public function __construct($entity = null)
	{
		if(isset($entity))
		{
			if($entity instanceof \CoreApi\Entity\BaseEntity)
			{
				$this->entity = $entity;
			}
			else
			{
				throw new \Exception("EntityValidator take an Entity as argument or no argument!");
			}
		}
		
		$this->init();
	}
	
	
	protected abstract function init();
	
	
	/**
	 * Returns the Element for the given ElementName.
	 * If Element does not exist jet, it is created.
	 * 
	 * @param string $elementName
	 * @return Element
	 */
	protected function get($elementName)
	{
		if(array_key_exists($elementName, $this->elements))
		{	return $this->elements[$elementName];	}
		
		$element = new \Core\Validator\Element();
		$this->elements[$elementName] = $element;
		
		return $element;
	}
	
	/**
	 * @see self::get
	 * @param string $elementName
	 * @return Element
	 */
	public function __get($elementName)
	{
		return $this->get($elementName);
	}
	
	
	public function isValid(Params $form, $map = null)
	{
		$isValid = true;
		
		foreach($this->elements as $entityElementName => $element)
		{
			$formElementName = $entityElementName;
			
			if(isset($map) && array_key_exists($entityElementName, $map))
			{	$formElementName = $map[$entityElementName];	}
			
			
			
			$elementValue = null;
			
			if(isset($this->entity))
			{	$elementValue = $this->getEntityValue($entityElementName);	}
			
			if(!is_null($form->getValue($formElementName)))
			{	$elementValue = $form->getValue($formElementName);	}
			
			if($element->isValid($elementValue))
			{	continue;	}
			else
			{
				$isValid = false;
				$messages = $element->getMessages();
				
				if($form->hasElement($formElementName))
				{
					$form->addError($formElementName, $messages);
				}
				else
				{
					// TODO: Add errors to Form
				}
			}
		}
		
		// TODO: Check, if array() as argument behaves as desired!!
		// (should take the values, which have been populated before by the Controller) 
		//$isValid = $form->isValid(array()) && $isValid;
		
		return $isValid;
	}
	
	
	public function apply(Params $form, $map = null)
	{
		if(is_null($this->entity))
		{	throw new \Exception("Apply can only be used, if Validator was constructed with an Entity!");	}
		
		foreach($this->elements as $entityElementName => $element)
		{
			$formElementName = $entityElementName;
				
			if(isset($map) && array_key_exists($entityElementName, $map))
			{	$formElementName = $map[$entityElementName];	}
			
			
			if(!is_null($form->getValue($formElementName)))
			{
				$elementValue = $form->getValue($formElementName);
				$this->setEntityValue($entityElementName, $elementValue);
			}
		}
	}
	
	public function applyIfValid(Params $form, $map = null)
	{
		if($this->isValid($form, $map))
		{
			$this->apply($form, $map);
			return true;
		}
		
		return false;
	}	
	
	private function getEntityValue($propertyName)
	{
		if(is_null($this->entity))
		{	return null;	}
		
		$getterName = "get" . ucfirst($propertyName);
		
		if(method_exists($this->entity, $getterName))
		{
			return $this->entity->{$getterName}();
		}
		else 
		{
			return null;
		}
	}
	
	private function setEntityValue($propertyName, $value)
	{
		$setterName = "set" . ucfirst($propertyName);
		if(method_exists($this->entity, $setterName))
		{
			return $this->entity->{$setterName}($value);
		}
		else
		{
			throw new \Exception("There is no setter for Property $propertyName.");
		}
	}
	
}