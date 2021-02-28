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
abstract class AbstractContentNodeOwner extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToOne(targetEntity="ContentNode")
     */
    private ?ContentNode $rootContentNode = null;

    /**
     * @ORM\OneToMany(targetEntity="ContentNode", mappedBy="owner")
     */
    private Collection $contentNodes;

    public function __construct() {
        parent::__construct();

        $this->contentNodes = new ArrayCollection();
    }

    public function getRootContentNode(): ?ContentNode {
        return $this->rootContentNode;
    }

    public function setRootContentNode(?ContentNode $rootContentNode) {
        $this->rootContentNode = $rootContentNode;
    }

    public function getContentNodes(): Collection {
        return $this->contentNodes;
    }

    public function addContentNode(ContentNode $contentNode) {
        $contentNode->setOwner($this);
        $this->contentNodes->add($contentNode);
    }

    public function removeContentNode(ContentNode $contentNode) {
        $contentNode->setOwner(null);
        $this->contentNodes->removeElement($contentNode);
    }
}
