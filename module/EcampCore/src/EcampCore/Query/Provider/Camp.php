<?php

namespace EcampCore\Query\Provider;


use ZF\Rest\ResourceEvent;
use ZF\Apigility\Doctrine\Server\Query\Provider\DefaultOrm;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class Camp extends DefaultOrm{
	
	public function createQuery(ResourceEvent $event, $entityClass, $parameters){
		
		$auth = new AuthenticationService();
		$auth->authenticate(new DummyAuthAdapter());
		
		$auth = new AuthenticationService();
		$auth->getIdentity();
		
		$qb = parent::createQuery($event, $entityClass, $parameters);
		$qb->andWhere('row.visibility = \'public\'');
		$qb->orWhere('row.creator = ?1')->setParameter(1,$auth->getIdentity());
		return $qb;
	}
	
}


class DummyAuthAdapter implements AdapterInterface{
	
	/**
	 * Performs an authentication attempt
	 *
	 * @return \Zend\Authentication\Result
	 * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
	 *               If authentication cannot be performed
	 */
	public function authenticate()
	{
		return new Result(Result::SUCCESS, 110);
	}
}