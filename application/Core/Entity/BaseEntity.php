<?php

namespace Core\Entity;

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

	
	public abstract function asReadonly();
	
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
	
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}
	
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	
	/**
	* update attributes of an entity by array
	*/
	public function updateAttributes($data)
	{
		foreach( $data as $key=>$value )
			$this->{"set".ucfirst($key)}($value);
	}
}