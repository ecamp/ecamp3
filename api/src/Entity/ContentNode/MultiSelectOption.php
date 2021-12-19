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
use App\Util\EntityMap;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MultiSelectOptionRepository::class)
 * @ORM\Table(name="content_node_multiselect_option")
 */
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
class MultiSelectOption extends BaseEntity implements BelongsToCampInterface, SortableEntityInterface {
    use SortableEntityTrait;

    /**
     * @ORM\ManyToOne(targetEntity="MultiSelect", inversedBy="options")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['read'])]
    public ?MultiSelect $multiSelect = null;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    #[Groups(['read'])]
    public string $translateKey;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    #[Groups(['read', 'update'])]
    public bool $checked = false;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->multiSelect?->getCamp();
    }

    /**
     * @param MultiSelectOption $prototype
     * @param EntityMap         $entityMap
     */
    public function copyFromPrototype($prototype, &$entityMap = null) {
        parent::copyFromPrototype($prototype, $entityMap);

        $this->translateKey = $prototype->translateKey;
        $this->checked = $prototype->checked;
        $this->setPos($prototype->getPos());
    }
}
