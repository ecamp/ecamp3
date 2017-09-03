<?php

namespace EcampApi\Hydrator;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Hydrator\HydrationInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CampHydrator extends DoctrineObject implements HydrationInterface{
	
	/**
	 * Constructor
	 *
	 * @param ObjectManager $objectManager The ObjectManager to use
	 */
	public function __construct(ObjectManager $objectManager)
	{
		parent::__construct($objectManager, true);
	
	}
	
	/**
	 * Extract values from an object
	 *
	 * @param  object $object
	 * @return array
	 */
	public function extract($object)
	{
		$ex = parent::extract($object);
		
		// FIXME: dummy example for a custom entity hydrator
		$ex['now'] = time();
	
		return $ex;
	}
}