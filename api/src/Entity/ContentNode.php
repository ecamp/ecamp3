<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ContentNodeRepository;
use App\Validator\AssertEitherIsNull;
use App\Validator\ContentNode\AssertBelongsToSameOwner;
use App\Validator\ContentNode\AssertContentTypeCompatible;
use App\Validator\ContentNode\AssertNoLoop;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * A piece of information that is part of a programme. ContentNodes may store content such as
 * one or multiple free text fields, or any other necessary data. Content nodes may also be used
 * to define layouts. For this purpose, a content node may offer so-called slots, into which other
 * content nodes may be inserted. In return, a content node may be nested inside a slot in a parent
 * container content node. This way, a tree of content nodes makes up a complete programme.
 *
 * @ORM\Entity(repositoryClass=ContentNodeRepository::class)
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="strategy", type="string")
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_authenticated()'],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['parent', 'contentType', 'root'])]
abstract class ContentNode extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToOne(targetEntity="AbstractContentNodeOwner", mappedBy="rootContentNode", cascade={"persist"})
     */
    #[SerializedName('_owner')]
    #[ApiProperty(readable: false, writable: false)]
    public ?AbstractContentNodeOwner $owner = null;

    /**
     * The content node that is the root of the content node tree. Refers to itself in case this
     * content node is the root.
     *
     * @ORM\ManyToOne(targetEntity="ContentNode", inversedBy="rootDescendants")
     * TODO make not null in the DB using a migration, and get fixtures to run
     * @ORM\JoinColumn(nullable=true)
     *
     * @Gedmo\SortableGroup - this is needed to avoid that all root nodes are in the same sort group (parent:null, slot: '')
     */
    #[ApiProperty(writable: false, example: '/content_nodes/1a2b3c4d')]
    #[Groups(['read'])]
    public ?ContentNode $root = null;

    /**
     * All content nodes that are part of this content node tree.
     *
     * @ORM\OneToMany(targetEntity="ContentNode", mappedBy="root")
     */
    #[ApiProperty(readable: false, writable: false)]
    public Collection $rootDescendants;

    /**
     * The parent to which this content node belongs. Is null in case this content node is the
     * root of a content node tree. For non-root content nodes, the parent can be changed, as long
     * as the new parent is in the same camp as the old one. A content node is defined as root when
     * it has an owner.
     *
     * @ORM\ManyToOne(targetEntity="ContentNode", inversedBy="children")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\SortableGroup
     */
    #[AssertEitherIsNull(
        other: 'owner',
        messageBothNull: 'Must not be null on non-root content nodes.',
        messageNoneNull: 'Must be null on root content nodes.'
    )]
    #[AssertBelongsToSameOwner(groups: ['update'])]
    #[AssertNoLoop(groups: ['update'])]
    #[ApiProperty(example: '/content_nodes/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    public ?ContentNode $parent = null;

    /**
     * All content nodes that are direct children of this content node.
     *
     * @ORM\OneToMany(targetEntity="ContentNode", mappedBy="parent", cascade={"persist"})
     */
    #[ApiProperty(writable: false, example: '["/content_nodes/1a2b3c4d"]')]
    #[Groups(['read'])]
    public Collection $children;

    /**
     * The name of the slot in the parent in which this content node resides. The valid slot names
     * are defined by the content type of the parent.
     *
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\SortableGroup
     */
    #[ApiProperty(example: 'footer')]
    #[Groups(['read', 'write'])]
    public ?string $slot = null;

    /**
     * A whole number used for ordering multiple content nodes that are in the same slot of the
     * same parent. The API does not guarantee the uniqueness of parent+slot+position.
     *
     * @ORM\Column(type="integer", nullable=false)
     * @Gedmo\SortablePosition
     */
    #[ApiProperty(example: '0')]
    #[Groups(['read', 'write'])]
    public int $position = -1;

    /**
     * An optional name for this content node. This is useful when planning e.g. an alternative
     * version of the programme suited for bad weather, in addition to the normal version.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[ApiProperty(example: 'Schlechtwetterprogramm')]
    #[Groups(['read', 'write'])]
    public ?string $instanceName = null;

    /**
     * Defines the type of this content node. There is a fixed list of types that are implemented
     * in eCamp. Depending on the type, different content data and different slots may be allowed
     * in a content node. The content type may not be changed once the content node is created.
     *
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ApiProperty(example: '/content_types/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[AssertContentTypeCompatible]
    public ?ContentType $contentType = null;

    public function __construct() {
        $this->rootDescendants = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * The name of the content type of this content node. Read-only, for convenience.
     */
    #[ApiProperty(example: 'SafetyConcept')]
    #[Groups(['read'])]
    public function getContentTypeName(): string {
        return $this->contentType?->name;
    }

    /**
     * The entity that owns the content node tree that this content node resides in.
     */
    #[SerializedName('owner')]
    #[ApiProperty(writable: false, example: '/activities/1a2b3c4d')]
    #[Groups(['read'])]
    public function getRootOwner(): Activity|Category|AbstractContentNodeOwner|null {
        if (null !== $this->root) {
            return $this->root->owner;
        }

        // this line is used during create process when $this->root is not yet set
        // returns null if parent is not set
        return $this->parent?->root->owner;
    }

    /**
     * The category that owns this content node's content tree, or the category of the
     * activity that owns this content node's content tree.
     *
     * @throws Exception when the owner is neither an activity nor a category
     */
    #[ApiProperty(example: '/categories/1a2b3c4d')]
    #[Groups(['read'])]
    public function getOwnerCategory(): Category {
        $owner = $this->getRootOwner();

        if ($owner instanceof Activity) {
            $owner = $owner->category;
        }

        if ($owner instanceof Category) {
            return $owner;
        }

        throw new Exception('Unexpected owner type '.get_debug_type($owner));
    }

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        $owner = $this->getRootOwner();
        if ($owner instanceof BelongsToCampInterface) {
            return $owner->getCamp();
        }

        return null;
    }

    /**
     * @return ContentNode[]
     */
    public function getRootDescendants(): array {
        return $this->rootDescendants->getValues();
    }

    public function addRootDescendant(self $rootDescendant): self {
        if (!$this->rootDescendants->contains($rootDescendant)) {
            $this->rootDescendants[] = $rootDescendant;
            $rootDescendant->root = $this;
        }

        return $this;
    }

    public function removeRootDescendant(self $rootDescendant): self {
        if ($this->rootDescendants->removeElement($rootDescendant)) {
            // reset the owning side (unless already changed)
            if ($rootDescendant->root === $this) {
                $rootDescendant->root = $rootDescendant;
            }
        }

        return $this;
    }

    /**
     * @return ContentNode[]
     */
    public function getChildren(): array {
        return $this->children->getValues();
    }

    public function addChild(self $child): self {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->parent = $this;
        }

        return $this;
    }

    public function removeChild(self $child): self {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->parent === $this) {
                $child->parent = null;
            }
        }

        return $this;
    }

    /**
     * @param ContentNode $prototype
     */
    public function copyFromPrototype($prototype) {
        // copy ContentNode base properties
        $this->contentType = $prototype->contentType;
        $this->instanceName = $prototype->instanceName;
        $this->slot = $prototype->slot;
        $this->position = $prototype->position;

        // deep copy children
        foreach ($prototype->getChildren() as $childPrototype) {
            $childClass = $childPrototype::class;

            // @var ContentNode $childContentNode
            $childContentNode = new $childClass();

            $this->addChild($childContentNode);
            $this->root->addRootDescendant($childContentNode);

            $childContentNode->copyFromPrototype($childPrototype);
        }
    }
}
