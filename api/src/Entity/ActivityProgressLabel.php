<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\InputFilter;
use App\Repository\ActivityProgressLabelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Progress labels in a camp.
 * To each activity one label can be assigned.
 */
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'
        ),
        new Patch(
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            security: 'is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => ['Default', 'update']]
        ),
        new Delete(
            validate: true,
            validationContext: ['groups' => ['delete']],
            security: 'is_granted("CAMP_MANAGER", object)'
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Post(
            validationContext: ['groups' => ['Default', 'create']],
            denormalizationContext: ['groups' => ['write', 'create']],
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            securityPostDenormalize: 'is_granted("CAMP_MANAGER", object)'
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    order: ['camp.id', 'position']
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['camp'])]
#[ORM\Entity(repositoryClass: ActivityProgressLabelRepository::class)]
#[ORM\UniqueConstraint(name: 'activity_progress_label_unique', columns: ['campid', 'position'])]
class ActivityProgressLabel extends BaseEntity implements BelongsToCampInterface, CopyFromPrototypeInterface {
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => [
            'read',
        ],
        'swagger_definition_name' => 'read',
    ];

    /**
     * The camp to which this label belongs.
     */
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Gedmo\SortableGroup]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'progressLabels')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Camp $camp = null;

    /**
     * All the programme this progress label is asigned.
     */
    #[Assert\Count(
        exactly: 0,
        exactMessage: 'It\'s not possible to delete a progress label as long as it has an activity linked to it.',
        groups: ['delete']
    )]
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'progressLabel', orphanRemoval: true)]
    public Collection $activities;

    #[ApiProperty(example: 0)]
    #[Gedmo\SortablePosition]
    #[Groups(['read', 'write'])]
    #[ORM\Column(name: 'position', type: 'integer', nullable: false)]
    public int $position = -1;

    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ApiProperty(example: 'Planned')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text')]
    public ?string $title = null;

    public function __construct() {
        parent::__construct();
        $this->activities = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    public function copyFromPrototype($prototype, $entityMap): void {
        $entityMap->add($prototype, $this);

        $this->position = $prototype->position;
        $this->title = $prototype->title;
    }
}
