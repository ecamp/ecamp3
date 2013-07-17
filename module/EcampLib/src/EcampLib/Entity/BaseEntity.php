<?php

namespace EcampLib\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseEntity
{
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="id", type="string", nullable=false)
     */
    protected $id;

    /**
     * @ var Uid
     * @ ORM\OneToOne(targetEntity="EcampLib\Entity\Uid", cascade={"persist", "remove"})
     * @ ORM\JoinColumn(name="id", nullable=true)
     */
    protected $uid;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->createdAt->setTimestamp(0);

        $this->updatedAt = new \DateTime();
        $this->updatedAt->setTimestamp(0);

        $this->uid = new UId($this->getClassname());
        $this->id = $this->uid->getId();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        $this->createdAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * @ORM\PreUpdate
     */
    public function PreUpdate()
    {
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function __toString()
    {
        return "[" . $this->getClassname() . "::" . $this->getId() . "]";
    }

    private function getClassname()
    {
        return ClassUtils::getRealClass(get_class($this));
    }

    /**
     * @param  unknown                                 $property
     * @return \Doctrine\Common\Collections\Collection
     */
    protected function getList($property)
    {
        if (property_exists($this, $property)) {
            $list = $this->{$property};
        } else {
            throw new \Exception("Unknown List");
        }

        if ($list instanceof Collection) {
            return $list;
        }

        return null;
    }
}
