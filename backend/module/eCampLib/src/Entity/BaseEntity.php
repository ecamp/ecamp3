<?php

namespace eCamp\Lib\Entity;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Mapping as ORM;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseEntity implements ResourceInterface {
    public function __construct() {
        $this->id = base_convert(crc32(uniqid()), 10, 16);

        $this->createTime = new \DateTime();
        $this->createTime->setTimestamp(0);

        $this->updateTime = new \DateTime();
        $this->updateTime->setTimestamp(0);
    }

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="id", type="string", nullable=false)
     */
    protected $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="create_time", type="datetime")
     */
    protected $createTime;

    /**
     * @var \DateTime
     * @ORM\Column(name="update_time", type="datetime")
     */
    protected $updateTime;


    public function getResourceId() {
        return ClassUtils::getClass($this);
    }


    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist() {
        $this->createTime = new \DateTime();
        $this->updateTime = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function PreUpdate() {
        $this->updateTime = new \DateTime();
    }

    public function __toString() {
        return "[" . $this->getClassname() . "::" . $this->getId() . "]";
    }

    private function getClassname() {
        return ClassUtils::getClass($this);
    }
}
