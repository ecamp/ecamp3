<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\ContentNode\ColumnLayout;
use App\Serializer\Normalizer\RelatedCollectionLink;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait HasRootContentNodeTrait {
    /**
     * The programme contents, organized as a tree of content nodes. The root content node cannot be
     * exchanged, but all the contents attached to it can.
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(writable: false, readableLink: true, example: '/content_nodes/1a2b3c4d')]
    #[ORM\OneToOne(targetEntity: ColumnLayout::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, unique: true)]
    public ?ColumnLayout $rootContentNode = null;

    public function setRootContentNode(?ColumnLayout $rootContentNode) {
        if (null !== $rootContentNode) {
            // make content node a root node
            $rootContentNode->addRootDescendant($rootContentNode);
        }

        $this->rootContentNode = $rootContentNode;
    }

    #[Assert\DisableAutoMapping]
    #[Groups(['read'])]
    public function getRootContentNode(): ?ColumnLayout {
        // Getter is here to add annotations to parent class property
        return $this->rootContentNode;
    }

    /**
     * All the content nodes that make up the tree of programme content.
     *
     * @return ContentNode[]
     */
    #[ApiProperty(example: '["/content_nodes/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[RelatedCollectionLink(ContentNode::class, ['root' => 'rootContentNode'])]
    public function getContentNodes(): array {
        return $this->rootContentNode?->getRootDescendants() ?? [];
    }
}
