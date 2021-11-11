<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\MaterialListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * A list of material items that someone needs to bring to the camp. A material list
 * is automatically created for each person collaborating on the camp.
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_fully_authenticated()'],
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
#[Entity(repositoryClass: MaterialListRepository::class)]
class MaterialList extends BaseEntity implements BelongsToCampInterface {
    /**
     * The items that are part of this list.
     */
    #[ApiProperty(writable: false, example: '["/material_items/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[OneToMany(targetEntity: 'MaterialItem', mappedBy: 'materialList')]
    public Collection $materialItems;

    /**
     * The camp this material list belongs to.
     */
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ManyToOne(targetEntity: 'Camp', inversedBy: 'materialLists')]
    #[JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Camp $camp = null;

    /**
     * The id of the material list that was used as a template for creating this camp. Internal
     * for now, is not published through the API.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[Column(type: 'string', length: 16, nullable: true)]
    public ?string $materialListPrototypeId = null;

    /**
     * The human readable name of the material list.
     */
    #[ApiProperty(example: 'Lebensmittel')]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: false)]
    public ?string $name = null;

    public function __construct() {
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
}
