<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;

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
    public ?ContentNode $rootContentNode = null;

    public function setRootContentNode(?ContentNode $rootContentNode) {
        // unset the owning side of the relation if necessary
        if (null === $rootContentNode && null !== $this->rootContentNode) {
            $this->rootContentNode->owner = null;
        }

        // set the owning side of the relation if necessary
        if (null !== $rootContentNode && $rootContentNode->owner !== $this) {
            $rootContentNode->owner = $this;
        }

        $this->rootContentNode = $rootContentNode;
    }

    /**
     * @return ContentNode[]
     */
    #[ApiProperty(writable: false)]
    public function getContentNodes(): array {
        return $this->rootContentNode?->getRootDescendants() ?? [];
    }
}
