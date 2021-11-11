<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\BaseEntity;
use App\Entity\BelongsToCampInterface;
use App\Entity\Camp;
use App\Entity\SortableEntityInterface;
use App\Entity\SortableEntityTrait;
use App\Repository\MultiSelectOptionRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    routePrefix: '/content_node',
    collectionOperations: [
        'get' => [
            'security' => 'is_fully_authenticated()',
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
)]
#[ApiFilter(SearchFilter::class, properties: ['multiSelect'])]
#[Entity(repositoryClass: MultiSelectOptionRepository::class)]
#[Table(name: 'content_node_multiselect_option')]
class MultiSelectOption extends BaseEntity implements BelongsToCampInterface, SortableEntityInterface {
    use SortableEntityTrait;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['read'])]
    #[ManyToOne(targetEntity: 'MultiSelect', inversedBy: 'options')]
    #[JoinColumn(nullable: false, onDelete: 'cascade')]
    public MultiSelect $multiSelect;

    #[Groups(['read'])]
    #[Column(type: 'text', nullable: false)]
    public string $translateKey;

    #[Groups(['read', 'update'])]
    #[Column(type: 'boolean', nullable: false)]
    public bool $checked = false;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->multiSelect?->getCamp();
    }
}
