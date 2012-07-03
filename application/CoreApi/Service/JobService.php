<?php

namespace CoreApi\Service;


use CoreApi\Entity\Job;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;
use CoreApi\Service\Params\Params;

use CoreApi\Entity\User;
use CoreApi\Entity\Login;


/**
 * @method CoreApi\Service\LoginService Simulate
 */
class JobService 
	extends ServiceBase
{
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl() { }
	
	
	public function AddJob(array $params){
		$context = $this->getContext();
		list($class, $job) = $params["callback"];
		
		$backgroundJob = new Job($context);
		$backgroundJob->setClass($class);
		$backgroundJob->setJob($job);
		$backgroundJob->setParams($params["params"]);
		$backgroundJob->setDescription($params["desc"]);
		
		$this->persist($backgroundJob);
	}
	
}
