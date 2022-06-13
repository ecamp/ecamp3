<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\InputFilter;
use App\Repository\MaterialListRepository;
use App\Util\EntityMap;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A list of material items that someone needs to bring to the camp. A material list
 * is automatically created for each person collaborating on the camp.
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_authenticated()'],
        'post' => [
            'denormalization_context' => ['groups' => ['write', 'create']],
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
        ],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
        'patch' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
        'delete' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['camp'])]
#[ORM\Entity(repositoryClass: MaterialListRepository::class)]
class MaterialList extends BaseEntity implements BelongsToCampInterface, CopyFromPrototypeInterface {
    /**
     * The items that are part of this list.
     */
    #[Assert\Count(
        exactly: 0,
        exactMessage: 'It\'s not possible to delete a material list as long as it has items linked to it.',
        groups: ['delete']
    )]
    #[ApiProperty(writable: false, example: '["/material_items/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[ORM\OneToMany(targetEntity: MaterialItem::class, mappedBy: 'materialList')]
    public Collection $materialItems;

    /**
     * The camp this material list belongs to.
     */
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'materialLists')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Camp $camp = null;

    /**
     * The campCollaboration this material list belongs to.
     */
    #[ApiProperty(writable: false, example: '/camp_collaborations/1a2b3c4d')]
    #[Groups(['read'])]
    #[ORM\OneToOne(targetEntity: CampCollaboration::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    public ?CampCollaboration $campCollaboration = null;

    /**
     * The id of the material list that was used as a template for creating this camp. Internal
     * for now, is not published through the API.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\Column(type: 'string', length: 16, nullable: true)]
    public ?string $materialListPrototypeId = null;

    /**
     * The human readable name of the material list.
     */
    #[ApiProperty(example: 'Lebensmittel')]
    #[Groups(['write'])]
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $name = null;

    public function __construct() {
        parent::__construct();
        $this->materialItems = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    /**
     * @return MaterialItem[]
     */
    public function getMaterialItems(): array {
        return $this->materialItems->getValues();
    }

    public function addMaterialItem(MaterialItem $materialItem): self {
        if (!$this->materialItems->contains($materialItem)) {
            $this->materialItems[] = $materialItem;
            $materialItem->materialList = $this;
        }

        return $this;
    }

    public function removeMaterialItem(MaterialItem $materialItem): self {
        if ($this->materialItems->removeElement($materialItem)) {
            if ($materialItem->materialList === $this) {
                $materialItem->materialList = null;
            }
        }

        return $this;
    }

    #[ApiProperty(example: 'Lebensmittel')]
    #[SerializedName('name')]
    #[Groups(['read'])]
    public function getName(): ?string {
        return $this->name
            ?? $this->campCollaboration?->user?->getDisplayName()
            ?? $this->campCollaboration?->inviteEmail
            ?? 'NoName';
    }

    /**
     * @param MaterialList $prototype
     * @param EntityMap    $entityMap
     */
    public function copyFromPrototype($prototype, $entityMap): void {
        $entityMap->add($prototype, $this);

        $this->materialListPrototypeId = $prototype->getId();
        $this->name = $prototype->getName();
    }
}
