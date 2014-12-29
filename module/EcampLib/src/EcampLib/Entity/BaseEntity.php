<?php

namespace EcampLib\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Util\ClassUtils;
use Zend\Form\Annotation as Form;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Form\Hydrator("DoctrineModule\StdLib\Hydrator\DoctrineObject")
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
     * @Form\Exclude
     */
    protected $id;

    /**
     * @ var Uid
     * @ ORM\OneToOne(targetEntity="EcampLib\Entity\Uid", cascade={"persist", "remove"})
     * @ ORM\JoinColumn(name="id", nullable=true)
     * @Form\Exclude
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
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function PreUpdate()
    {
        $this->updatedAt = new \DateTime();
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
        return ClassUtils::getClass($this);
    }

    /**
     * @param  string     $listProperty
     * @param  object     $element
     * @throws \Exception
     */
    protected function addToList($listProperty, $element)
    {
        if (property_exists($this, $listProperty)) {
            /* @var $list \Doctrine\Common\Collections\ArrayCollection */
            $list = $this->{$listProperty};
        } else {
            throw new \Exception("Unknown List");
        }

        if (!$list->contains($element)) {
            $list->add($element);
        }
    }

    /**
     * @param  string     $listProperty
     * @param  object     $element
     * @throws \Exception
     */
    protected function removeFromList($listProperty, $element)
    {
        $methodName = 'get' . ucfirst($listProperty);

        if (method_exists($this, $methodName)) {
            /* @var $list \Doctrine\Common\Collections\ArrayCollection */
            $list = $this->$methodName();
        } elseif (property_exists($this, $listProperty)) {
            /* @var $list \Doctrine\Common\Collections\ArrayCollection */
            $list = $this->$listProperty;
        } else {
            throw new \Exception("Unknown List");
        }

        $list->removeElement($element);
    }
}
