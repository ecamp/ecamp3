<?php

namespace Entity;

/**
 * @MappedSuperclass 
 * @HasLifecycleCallbacks
 */
abstract class BaseEntity
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	public function __construct()
	{
		$this->em = \Zend_Registry::get('doctrine')->getEntityManager();
	}

	public function __clone()
	{
		$this->em = \Zend_Registry::get('doctrine')->getEntityManager();
	}

	
	/** @Column(name="created_at", type="datetime") */
	private $createdAt;

	/** @Column(name="updated_at", type="datetime") */
	private $updatedAt;

	/**
	 * @PrePersist
	 */
	public function PrePersist()
	{
		$this->createdAt = new \DateTime("now");
		$this->updatedAt = new \DateTime("now");
	}

	/**
	 * @PreUpdate
	 */
	public function PreUpdate()
	{
		$this->updatedAt = new \DateTime("now");
	}
}