<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Core\Job;

use CoreApi\Entity\Job;
use CoreApi\Entity\EntityRef;


class JobRunner
{
	
	/**
	 * @var Core\Repository\JobRepository
	 * @Inject Core\Repository\JobRepository
	 */
	private $jobRepo;
	
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	private $contextProvider;
	
	/**
	 * @var Core\Acl\DefaultAcl
	 * @Inject Core\Acl\DefaultAcl
	 */
	private $acl;
	
	/**
	 * @var PhpDI\IKernel
	 * @Inject PhpDI\IKernel
	 */
	private $kernel;
	
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	private $em;
	
	
	/**
	 * @var CoreApi\Entity\Job
	 */
	private $job = null;
	
	
	
	public function init()
	{
		$kernel = \Zend_Registry::get('kernel');
		$kernel->Inject($this);
		
		$this->acl->setIsJob(true);
		register_shutdown_function(array($this, 'shotdown'));
	}
	
	public function shotdown(){
		$e = error_get_last();
		
		if($this->job !== null && $e !== null){
			
			$this->job->setStatus(Job::JOB_FAILED);
			$this->job->setErrorMessage(print_r(error_get_last(), true));
			
			$this->em->flush($this->job);
		}
		
	}
	
	public function run()
	{
		while($this->job = $this->getNextJob()){
			$this->runJob();
		}
	}
	
	/**
	 * @return CoreApi\Entity\Job
	 */
	private function getNextJob()
	{
		return $this->jobRepo->findOneBy(array('status' => Job::JOB_CREATED));
	}
	
	
	private function runJob()
	{
		$this->job->setExecTime(new \DateTime("now"));
		$this->job->setStatus(Job::JOB_FAILED);
		$this->em->flush($this->job);
		
		\Zend_Auth::getInstance()->getStorage()->write($this->job->getMeId());
		$this->contextProvider->set(
			$this->job->getUserId(), $this->job->getGroupId(), $this->job->getCampId());
		
		$jobObject = $this->kernel->Get($this->job->getClass());
		$params = $this->insertEntities($this->job->getParams());
		
		call_user_func_array(array($jobObject, $this->job->getMethod()), $params);
		
		$this->job->setStatus(Job::JOB_SUCCEEDED);
		$this->em->flush($this->job);
			
		$this->job = null;
	}
	
	
	private function insertEntities(array $params)
	{
		foreach($params as &$param){
			
			if($param instanceof EntityRef){
				$param = $this->em->find($param->class, $param->id);
			}
			elseif(is_array($param)){
				$param = $this->insertEntities($param);
			}
		}
	
		return $params;
	}
	
}