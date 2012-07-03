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
		
		register_shutdown_function(array($this, 'shutdown'));
	}
	
	
	public function shutdown()
	{
		if($this->job != null){
			$this->job->setStatus(Job::JOB_FAILED);
			$this->job->setErrorMessage(print_r(error_get_last(), true));
			$this->em->persist($this->job);
			$this->em->flush($this->job);
		}
		
		exit;
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
		$this->job->setStatus(Job::JOB_RUNNING);
		$this->em->flush($this->job);
		
		try{
			\Zend_Auth::getInstance()->getStorage()->write($this->job->getMeId());
			$this->contextProvider->set(
				$this->job->getUserId(), $this->job->getGroupId(), $this->job->getCampId());
						
			$jobClass = $this->job->getClass();
			$jobObject = new $jobClass();
			$this->kernel->Inject($jobObject);

			call_user_func(array($jobObject, $this->job->getJob()), $this->job->getParams());
			$this->job->setStatus(Job::JOB_SUCCEEDED);
		}
		catch (\Exception $e){
			$this->job->setStatus(Job::JOB_FAILED);
			$this->job->setErrorMessage($e->getMessage());
		}
		
		$this->em->flush($this->job);
		$this->job = null;
	}
	
}