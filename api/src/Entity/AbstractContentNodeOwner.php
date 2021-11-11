<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity]
#[InheritanceType(value: 'JOINED')]
#[DiscriminatorColumn(name: 'entityType', type: 'string')]
abstract class AbstractContentNodeOwner extends BaseEntity {
    /**
     * The programme contents, organized as a tree of content nodes. The root content node cannot be
     * exchanged, but all the contents attached to it can.
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(writable: false, example: '/content_nodes/1a2b3c4d')]
    #[OneToOne(targetEntity: 'ContentNode', inversedBy: 'owner', cascade: ['persist'])]
    #[JoinColumn(nullable: false, unique: true, onDelete: 'cascade')]
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
