<?php
/*
 * Copyright (C) 2011 Urban Suppiger
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

namespace CoreApi\Entity;

/**
 * @Entity(repositoryClass="\Core\Repository\JobRepository")
 * @Table(name="jobs")
 */
use CoreApi\Acl\Context;

class Job extends BaseEntity
{
	const JOB_CREATED 	= 'CREATED';
	const JOB_RUNNING 	= 'RUNNING';
	const JOB_SUCCEEDED = 'SUCCEEDED';
	const JOB_FAILED 	= 'FAILED';
	
	
	/**
	 * @var int
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Background Job Description
	 * @Column(type="text", nullable=true)
	 */
	private $description;

	/**
	 * BackgroundProcess Class
	 * @Column(type="string")
	 */
	private $class;
	
	/**
	 * BackgroundProcess Method
	 * @Column(type="string")
	 */
	private $job;
	
	/**
	 * BackgroundProcess Params
	 * @Column(type="text", nullable=true)
	 */
	private $params = null;
	
	/**
	 * BackgroundProcess Method
	 * @Column(type="string")
	 */
	private $status = self::JOB_CREATED;
	
	/**
	 * Execution Time
	 * @Column(name="exec_time", type="datetime", nullable=true)
	 */
	private $execTime = null;

	/**
	 * ErrorMessage
	 * @Column(name="error_message", type="text", nullable=true)
	 */
	private $errorMessage = null;
	
	/**
	 * Context Me
	 * @Column(type="string", nullable=true)
	 */
	private $context_me;
	
	/**
	 * Context User
	 * @Column(type="string", nullable=true)
	 */
	private $context_user;
	
	/**
	 * Context Camp
	 * @Column(type="string", nullable=true)
	 */
	private $context_camp;
	
	/**
	 * Context Group
	 * @Column(type="string", nullable=true)
	 */
	private $context_group;
	
	
	public function __construct(Context $context){
		$this->context_me 		= is_null($context->getMe()) 	? null : $context->getMe()->getId();
		$this->context_user 	= is_null($context->getUser()) 	? null : $context->getUser()->getId();
		$this->context_camp 	= is_null($context->getCamp()) 	? null : $context->getCamp()->getId();
		$this->context_group 	= is_null($context->getGroup()) ? null : $context->getGroup()->getId();
	}
	
	
	public function getId()
	{
		return $this->id;
	}
	
	
	/**
	 * @param string $description
	 */
	public function setDescription($description){
		$this->description = $description;
	}
	
	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->description;
	}
	
	
	/**
	 * @param string $description
	 */
	public function setClass($class){
		$this->class = $class;
	}
	
	/**
	 * @return string
	 */
	public function getClass(){
		return $this->class;
	}

	
	/**
	 * @param string $description
	 */
	public function setJob($job){
		$this->job = $job;
	}
	
	/**
	 * @return string
	 */
	public function getJob(){
		return $this->job;
	}
	
	
	/**
	 * @param array $params
	 */
	public function setParams(array $params){
		$this->params = is_null($params) ? null : json_encode($params);
	}
	
	/**
	 * @return array
	 */
	public function getParams(){
		return is_null($this->params) ? null : json_decode($this->params);
	}
	
	
	/**
	 * @param string $status
	 */
	public function setStatus($status){
		$this->status = $status;
	}
	
	/**
	 * @return string
	 */
	public function getStatus(){
		return $this->status;
	}
	
	
	/**
	 * @param DateTime $execTime
	 */
	public function setExecTime(\DateTime $execTime){
		$this->execTime = $execTime;
	}
	
	/**
	 * @return DateTime
	 */
	public function getExecTime(){
		return $this->execTime;
	}
	
	
	/**
	 * @param string $errorMessage
	 */
	public function setErrorMessage($errorMessage){
		$this->errorMessage = $errorMessage;
	}
	
	/**
	 * @return string
	 */
	public function getErrorMessage(){
		return $this->errorMessage;
	}
	
	
	/**
	 * @return int
	 */
	public function getMeId(){
		return $this->context_me;
	}
	
	/**
	 * @return int
	 */
	public function getUserId(){
		return $this->context_user;
	}
	
	/**
	 * @return int
	 */
	public function getCampId(){
		return $this->context_camp;
	}
	
	/**
	 * @return int
	 */
	public function getGroupId(){
		return $this->context_group;
	}
}
