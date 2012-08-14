<?php

namespace CoreApi\Entity;

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
	 * @var string	 
	 * @Id
	 * @Column(name="id", type="string")
	 */
	protected $id;
	
	public function __construct()
	{
		$this->createdAt = new \DateTime();
		$this->createdAt->setTimestamp(0);
		
		$this->updatedAt = new \DateTime();
		$this->updatedAt->setTimestamp(0);
	}
	

	public function getId()
	{
		return $this->id;
	}
	
	
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
	 * @deprecated
	 */
	public function updateAttributes($data)
	{
		foreach( $data as $key=>$value )
			$this->{"set".ucfirst($key)}($value);
	}
	
	
	public function __toString()
	{
		return "[" . get_class($this) . " #" . $this->getId() . "]";
	}
}