<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\BaseEntity;
use App\Entity\SortableEntityInterface;
use App\Entity\SortableEntityTrait;
use App\InputFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_node_storyboard_section")
 */
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
class StoryboardSection extends BaseEntity implements SortableEntityInterface {
    use SortableEntityTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Storyboard", inversedBy="sections")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['read', 'create'])]
    public Storyboard $storyboard;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\CleanHTML]
    #[Groups(['read', 'write'])]
    public ?string $column1 = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\CleanHTML]
    #[Groups(['read', 'write'])]
    public ?string $column2 = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\CleanHTML]
    #[Groups(['read', 'write'])]
    public ?string  $column3 = null;
}
