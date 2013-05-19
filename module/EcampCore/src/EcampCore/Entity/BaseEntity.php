<?php

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass 
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseEntity
{
	/** @ORM\Column(name="created_at", type="datetime") */
	private $createdAt;

	/** @ORM\Column(name="updated_at", type="datetime") */
	private $updatedAt;
	
	/**
	 * @var string	 
	 * @ORM\Id
	 * @ORM\Column(name="id", type="string", nullable=false)
	 */
	protected $id;
	
	
	/**
	 * @var Uid
	 * @ORM\OneToOne(targetEntity="EcampCore\Entity\Uid", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="id", nullable=true)
	 */
	protected $uid;
	
	
	public function __construct(){
		$this->createdAt = new \DateTime();
		$this->createdAt->setTimestamp(0);
		
		$this->updatedAt = new \DateTime();
		$this->updatedAt->setTimestamp(0);
		
		$this->uid = new UId(get_class($this));
		$this->id = $this->uid->getId();
	}
	

	public function getId(){
		return $this->id;
	}
	
	
	/**
	 * @ORM\PrePersist
	 */
	public function PrePersist(){
		$this->createdAt = new \DateTime("now");
		$this->updatedAt = new \DateTime("now");
	}

	/**
	 * @ORM\PreUpdate
	 */
	public function PreUpdate(){
		$this->updatedAt = new \DateTime("now");
	}
	
	
	public function getUpdatedAt(){
		return $this->updatedAt;
	}
	
	public function getCreatedAt(){
		return $this->createdAt;
	}
	
	
	public function __toString()
	{
		return "[" . get_class($this) . " #" . $this->getId() . "]";
	}
}