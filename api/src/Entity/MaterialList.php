<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * A list of material items that someone needs to bring to the camp. A material list
 * is automatically created for each person collaborating on the camp.
 *
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: [
        'get',
        'patch' => ['denormalization_context' => [
            'groups' => ['materialList:update'],
            'allow_extra_attributes' => false,
        ]],
        'delete',
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['camp'])]
class MaterialList extends BaseEntity implements BelongsToCampInterface {
    /**
     * The items that are part of this list.
     *
     * @ORM\OneToMany(targetEntity="MaterialItem", mappedBy="materialList")
     */
    #[ApiProperty(writable: false, example: '["/material_items/1a2b3c4d"]')]
    public Collection $materialItems;

    /**
     * The camp this material list belongs to.
     *
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="materialLists")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['Default'])]
    public ?Camp $camp = null;

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
     * @ORM\Column(type="text", nullable=false)
     */
    #[ApiProperty(example: 'Lebensmittel')]
    #[Groups(['Default', 'materialList:update'])]
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
