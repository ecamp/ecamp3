<?php

namespace eCamp\Lib\Entity;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Types\DateTimeUtc;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseEntity implements ResourceInterface {
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false)
     */
    protected $id;

    /**
     * @var DateTimeUtc
     * @ORM\Column(type="datetime")
     */
    protected $createTime;

    /**
     * @var DateTimeUtc
     * @ORM\Column(type="datetime")
     */
    protected $updateTime;

    public function __construct() {
        $this->id = base_convert(crc32(uniqid()), 10, 16);

        $this->createTime = new DateTimeUtc();
        $this->updateTime = new DateTimeUtc();
    }

    public function __toString() {
        return '['.$this->getClassname().'::'.$this->getId().']';
    }

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
        $this->createTime = new DateTimeUtc();
        $this->updateTime = new DateTimeUtc();
    }

    /**
     * @ORM\PreUpdate
     */
    public function PreUpdate() {
        $this->updateTime = new DateTimeUtc();
    }

    private function getClassname() {
        return ClassUtils::getClass($this);
    }
}
