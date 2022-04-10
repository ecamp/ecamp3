<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\MaterialListRepository;
use App\Util\EntityMap;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * A list of material items that someone needs to bring to the camp. A material list
 * is automatically created for each person collaborating on the camp.
 *
 * @ORM\Entity(repositoryClass=MaterialListRepository::class)
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
class MaterialList extends BaseEntity implements BelongsToCampInterface, CopyFromPrototypeInterface {
    /**
     * The items that are part of this list.
     *
     * @ORM\OneToMany(targetEntity="MaterialItem", mappedBy="materialList")
     */
    #[ApiProperty(writable: false, example: '["/material_items/1a2b3c4d"]')]
    #[Groups(['read'])]
    public Collection $materialItems;

    /**
     * The camp this material list belongs to.
     *
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="materialLists")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    public ?Camp $camp = null;

    /**
     * The campCollaboration this material list belongs to.
     *
     * @ORM\OneToOne(targetEntity="CampCollaboration")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    #[ApiProperty(writable: false, example: '/camp_collaborations/1a2b3c4d')]
    #[Groups(['read'])]
    public ?CampCollaboration $campCollaboration = null;

    /**
     * The id of the material list that was used as a template for creating this camp. Internal
     * for now, is not published through the API.
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    #[ApiProperty(readable: false, writable: false)]
    public ?string $materialListPrototypeId = null;

    /**
     * The human readable name of the material list.
     *
     * @ORM\Column(type="text")
     */
    #[ApiProperty(example: 'Lebensmittel')]
    #[Groups(['write'])]
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
