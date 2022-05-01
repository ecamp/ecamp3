<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\BaseEntity;
use App\Entity\BelongsToContentNodeInterface;
use App\Entity\CopyFromPrototypeInterface;
use App\Entity\SortableEntityInterface;
use App\Entity\SortableEntityTrait;
use App\InputFilter;
use App\Repository\StoryboardSectionRepository;
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
    order: ['storyboard.id', 'position']
)]
#[ApiFilter(SearchFilter::class, properties: ['storyboard'])]
#[ORM\Entity(repositoryClass: StoryboardSectionRepository::class)]
#[ORM\Table(name: 'content_node_storyboard_section')]
class StoryboardSection extends BaseEntity implements BelongsToContentNodeInterface, SortableEntityInterface, CopyFromPrototypeInterface {
    use SortableEntityTrait;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Gedmo\SortableGroup]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Storyboard::class, inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Storyboard $storyboard = null;

    #[InputFilter\CleanHTML]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $column1 = null;

    #[InputFilter\CleanHTML]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $column2 = null;

    #[InputFilter\CleanHTML]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string  $column3 = null;

    #[ApiProperty(readable: false)]
    public function getRoot(): ?ColumnLayout {
        return $this->storyboard?->getRoot();
    }

    /**
     * @param StoryboardSection $prototype
     * @param EntityMap         $entityMap
     */
    public function copyFromPrototype($prototype, $entityMap): void {
        $entityMap->add($prototype, $this);

        $this->column1 = $prototype->column1;
        $this->column2 = $prototype->column2;
        $this->column3 = $prototype->column3;
        $this->setPosition($prototype->getPosition());
    }
}
