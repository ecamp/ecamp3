<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\ContentNode;
use App\Entity\MaterialItem;
use App\Repository\MaterialNodeRepository;
use App\State\ContentNode\ContentNodePersistProcessor;
use App\Util\EntityMap;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'
        ),
        new Patch(
            processor: ContentNodePersistProcessor::class,
            denormalizationContext: ['groups' => ['write', 'update']],
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => ['Default', 'update']]
        ),
        new Delete(
            security: '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.parent !== null'
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Post(
            processor: ContentNodePersistProcessor::class,
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => ['Default', 'create']]
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    routePrefix: '/content_node'
)]
#[ORM\Entity(repositoryClass: MaterialNodeRepository::class)]
class MaterialNode extends ContentNode {
    /**
     * Holds the actual data of the content node
     * (overridden from abstract class in order to add specific validation).
     */
    #[ApiProperty(example: null)]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    #[Assert\IsNull]
    public ?array $data = null;

    #[ApiProperty(readableLink: true, writableLink: false)]
    #[Groups(['read'])]
    #[ORM\OneToMany(targetEntity: MaterialItem::class, mappedBy: 'materialNode', orphanRemoval: true, cascade: ['persist', 'remove'])]
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
