<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ContentNode;
use App\Entity\MaterialItem;
use App\Repository\MaterialNodeRepository;
use App\Util\EntityMap;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MaterialNodeRepository::class)
 * @ORM\Table(name="content_node_materialnode")
 */
#[ApiResource(
    routePrefix: '/content_node',
    collectionOperations: [
        'get' => [
            'security' => 'is_authenticated()',
        ],
        'post' => [
            'denormalization_context' => ['groups' => ['write', 'create']],
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ['Default', 'create'],
        ],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
        'patch' => [
            'denormalization_context' => ['groups' => ['write', 'update']],
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ['Default', 'update'],
        ],
        'delete' => ['security' => '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.owner === null'], // disallow delete when contentNode is a root node
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
class MaterialNode extends ContentNode {
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MaterialItem", mappedBy="materialNode", orphanRemoval=true, cascade={"persist", "remove"})
     */
    #[ApiProperty(readableLink: true, writableLink: false)]
    #[Groups(['read'])]
    public Collection $materialItems;

    public function __construct() {
        parent::__construct();
        $this->materialItems = new ArrayCollection();

        parent::__construct();
    }

    /**
     * @return MaterialItem[]
     */
    public function getMaterialItems(): array {
        return $this->materialItems->getValues();
    }

    public function addMaterialItem(MaterialItem $materialItem): self {
        if (!$this->materialItems->contains($materialItem)) {
            $this->materialItems->add($materialItem);
            $materialItem->materialNode = $this;
        }

        return $this;
    }

    public function removeMaterialItem(MaterialItem $materialItem): self {
        if ($this->materialItems->removeElement($materialItem)) {
            if ($materialItem->materialNode === $this) {
                $materialItem->materialNode = null;
            }
        }

        return $this;
    }

    /**
     * @param MaterialNode $prototype
     * @param EntityMap    $entityMap
     */
    public function copyFromPrototype($prototype, $entityMap): void {
        parent::copyFromPrototype($prototype, $entityMap);

        // copy all material items
        foreach ($prototype->materialItems as $itemPrototype) {
            $materialItem = new MaterialItem();
            $this->addMaterialItem($materialItem);

            $materialItem->copyFromPrototype($itemPrototype, $entityMap);
        }
    }
}
