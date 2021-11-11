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
use App\InputFilter\CleanHTML;
use App\Repository\StoryboardSectionRepository;
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
        'post' => [
            'denormalization_context' => ['groups' => ['write', 'create']],
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)', ],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
        'patch' => [
            'denormalization_context' => ['groups' => ['write', 'update']],
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
        ],
        'delete' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['storyboard'])]
#[Entity(repositoryClass: StoryboardSectionRepository::class)]
#[Table(name: 'content_node_storyboard_section')]
class StoryboardSection extends BaseEntity implements BelongsToCampInterface, SortableEntityInterface {
    use SortableEntityTrait;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['read', 'create'])]
    #[ManyToOne(targetEntity: 'Storyboard', inversedBy: 'sections')]
    #[JoinColumn(nullable: false, onDelete: 'cascade')]
    public Storyboard $storyboard;

    #[CleanHTML]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: true)]
    public ?string $column1 = null;

    #[CleanHTML]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: true)]
    public ?string $column2 = null;

    #[CleanHTML]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: true)]
    public ?string  $column3 = null;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->storyboard?->getCamp();
    }
}
