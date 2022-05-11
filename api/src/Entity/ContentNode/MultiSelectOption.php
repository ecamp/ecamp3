<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\BaseEntity;
use App\Entity\BelongsToContentNodeTreeInterface;
use App\Entity\CopyFromPrototypeInterface;
use App\Entity\SortableEntityInterface;
use App\Entity\SortableEntityTrait;
use App\Repository\MultiSelectOptionRepository;
use App\Util\EntityMap;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    routePrefix: '/content_node',
    collectionOperations: [
        'get' => [
            'security' => 'is_authenticated()',
        ],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
        'patch' => [
            'denormalization_context' => ['groups' => ['write', 'update']],
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
        ],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    order: ['multiSelect.id', 'position']
)]
#[ApiFilter(SearchFilter::class, properties: ['multiSelect'])]
#[ORM\Entity(repositoryClass: MultiSelectOptionRepository::class)]
#[ORM\Table(name: 'content_node_multiselect_option')]
class MultiSelectOption extends BaseEntity implements BelongsToContentNodeTreeInterface, SortableEntityInterface, CopyFromPrototypeInterface {
    use SortableEntityTrait;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Gedmo\SortableGroup]
    #[Groups(['read'])]
    #[ORM\ManyToOne(targetEntity: MultiSelect::class, inversedBy: 'options')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?MultiSelect $multiSelect = null;

    #[Groups(['read'])]
    #[ORM\Column(type: 'text', nullable: false)]
    public string $translateKey;

    #[Groups(['read', 'update'])]
    #[ORM\Column(type: 'boolean', nullable: false)]
    public bool $checked = false;

    #[ApiProperty(readable: false)]
    public function getRoot(): ?ColumnLayout {
        return $this->multiSelect?->getRoot();
    }

    /**
     * @param MultiSelectOption $prototype
     * @param EntityMap         $entityMap
     */
    public function copyFromPrototype($prototype, $entityMap): void {
        $entityMap->add($prototype, $this);

        $this->translateKey = $prototype->translateKey;
        $this->checked = $prototype->checked;
        $this->setPosition($prototype->getPosition());
    }
}
