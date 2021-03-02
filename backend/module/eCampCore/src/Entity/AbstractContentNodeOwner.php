<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="entityType", type="string")
 */
abstract class AbstractContentNodeOwner extends BaseEntity {
    /**
     * @ORM\OneToOne(targetEntity="ContentNode", inversedBy="owner")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ContentNode $rootContentNode = null;

    public function getRootContentNode(): ?ContentNode {
        return $this->rootContentNode;
    }

    public function setRootContentNode(?ContentNode $rootContentNode) {
        if ($this->rootContentNode !== $rootContentNode) {
            if (null != $this->rootContentNode) {
                $this->rootContentNode->setOwner(null);
            }
            $this->rootContentNode = $rootContentNode;
            if (null != $this->rootContentNode) {
                $this->rootContentNode->setOwner($this);
            }
        }
    }

    public function getAllContentNodes(): Collection {
        if (null != $this->rootContentNode) {
            return new ArrayCollection(array_merge(
                [$this->rootContentNode],
                $this->rootContentNode->getAllChildren()->toArray()
            ));
        }

        return new ArrayCollection();
    }
}
