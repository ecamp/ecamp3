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
	
	
	public function Add(Job $job){
		$context = $this->getContext();
		$job->setContext($context);
		
		$this->persist($job);
	}
	
}
