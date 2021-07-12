<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="entityType", type="string")
 */
abstract class AbstractContentNodeOwner extends BaseEntity {
    /**
     * The programme contents, organized as a tree of content nodes. The root content node cannot be
     * exchanged, but all the contents attached to it can.
     *
     * @ORM\OneToOne(targetEntity="ContentNode", inversedBy="owner", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, unique=true)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(writable: false, example: '/content_nodes/1a2b3c4d')]
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
     * All the content nodes that make up the tree of programme content.
     *
     * @return ContentNode[]
     */
    #[ApiProperty(example: '["/content_nodes/1a2b3c4d"]')]
    public function getContentNodes(): array {
        return $this->rootContentNode?->getRootDescendants() ?? [];
    }
}
