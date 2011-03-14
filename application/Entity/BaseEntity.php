<?php

namespace Entity;

/**
 * @MappedSuperclass 
 * @HasLifecycleCallbacks
 */
abstract class BaseEntity
{
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