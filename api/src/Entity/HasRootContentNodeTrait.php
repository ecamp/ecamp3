<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\ContentNode\ColumnLayout;
use App\Serializer\Normalizer\RelatedCollectionLink;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

trait HasRootContentNodeTrait {
    /**
     * The programme contents, organized as a tree of content nodes. The root content node cannot be
     * exchanged, but all the contents attached to it can.
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(writable: false, readableLink: true, example: '/content_nodes/1a2b3c4d')]
    #[ORM\OneToOne(targetEntity: ColumnLayout::class, cascade: ['persist'], fetch: 'EAGER')]
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
     * @return ContentNode[]
     */
    #[ApiProperty]
    #[SerializedName('contentNodes')]
    #[RelatedCollectionLink(ContentNode::class, ['root' => 'rootContentNode'])]
    #[Groups(['read'])]
    public function getEmptyContentNodes() {
        return [];
    }

    /**
     * All the content nodes that make up the tree of programme content.
     *
     * @return ContentNode[]
     */
    #[Assert\DisableAutoMapping]
    public function getContentNodes(): array {
        return $this->rootContentNode?->getRootDescendants() ?? [];
    }
}
