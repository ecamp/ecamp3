<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ActivityRepository;
use App\Serializer\Normalizer\RelatedCollectionLink;
use App\Validator\AssertBelongsToSameCamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A piece of programme that will be carried out once or multiple times in a camp.
 */
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read', 'Activity:ActivityResponsibles']],
            'security' => 'is_authenticated()',
        ],
        'post' => [
            'validation_groups' => ['Default', 'create'],
            'denormalization_context' => ['groups' => ['write', 'create']],
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)',
        ],
        'patch' => [
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ['Default', 'update'],
        ],
        'delete' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['camp'])]
#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity extends AbstractContentNodeOwner implements BelongsToCampInterface {
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => [
            'read',
            'Activity:Category',
            'Activity:ActivityResponsibles',
            'Activity:ScheduleEntries',
            'Activity:ContentNodes',
        ],
        'swagger_definition_name' => 'read',
    ];

    /**
     * The list of points in time when this activity's programme will be carried out.
     */
    #[Assert\Valid]
    #[Assert\Count(min: 1, groups: ['create'])]
    #[ApiProperty(
        writableLink: true,
        example: '[{ "period": "/periods/1a2b3c4a", "endOffset": 1100, "startOffset": 1000 }]',
    )]
    #[Groups(['read', 'create'])]
    #[ORM\OneToMany(targetEntity: ScheduleEntry::class, mappedBy: 'activity', orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(['startOffset' => 'ASC', 'left' => 'ASC', 'endOffset' => 'DESC', 'id' => 'ASC'])]
    public Collection $scheduleEntries;

    /**
     * The camp to which this activity belongs.
     */
    #[Assert\DisableAutoMapping] // camp is set in the DataPersister
    #[ApiProperty(writable: false, example: '/camps/1a2b3c4d')]
    #[Groups(['read'])]
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Camp $camp = null;

    /**
     * The category to which this activity belongs. The category determines color and numbering scheme
     * of the activity, and is used for marking similar activities. Must be in the same camp as the activity.
     */
    #[ApiProperty(example: '/categories/1a2b3c4d')]
    #[AssertBelongsToSameCamp(groups: ['update'])]
    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    public ?Category $category = null;

    /**
     * The title of this activity that is shown in the picasso.
     */
    #[ApiProperty(example: 'Sportolympiade')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text')]
    public ?string $title = null;

    /**
     * The physical location where this activity's programme will be carried out.
     */
    #[ApiProperty(example: 'Spielwiese')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text')]
    public string $location = '';

    /**
     * The list of people that are responsible for planning or carrying out this activity.
     */
    #[ApiProperty(writable: false)]
    #[Groups(['read'])]
    #[ORM\OneToMany(targetEntity: ActivityResponsible::class, mappedBy: 'activity', orphanRemoval: true)]
    private Collection $activityResponsibles;

    public function __construct() {
        parent::__construct();
        $this->scheduleEntries = new ArrayCollection();
        $this->activityResponsibles = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp ?? $this->category?->camp;
    }

    /**
     * @return Category
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('category')]
    #[Groups('Activity:Category')]
    public function getEmbeddedCategory(): ?Category {
        return $this->category;
    }

    #[ApiProperty(writable: false)]
    public function setRootContentNode(?ContentNode $rootContentNode) {
        // Overridden to add annotations
        parent::setRootContentNode($rootContentNode);
    }

    #[Assert\DisableAutoMapping]
    #[Groups(['read'])]
    public function getRootContentNode(): ?ContentNode {
        // Getter is here to add annotations to parent class property
        return $this->rootContentNode;
    }

    /**
     * @return ContentNode[]
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('contentNodes')]
    #[Groups(['Activity:ContentNodes'])]
    public function getEmbeddedContentNodes(): array {
        return $this->getContentNodes();
    }

    /**
     * Overridden in order to add annotations.
     *
     * {@inheritdoc}
     *
     * @return ContentNode[]
     */
    #[Groups(['read'])]
    #[RelatedCollectionLink(ContentNode::class, ['root' => 'rootContentNode'])]
    public function getContentNodes(): array {
        return parent::getContentNodes();
    }

    /**
     * @return ScheduleEntry[]
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('scheduleEntries')]
    #[Groups(['Activity:ScheduleEntries'])]
    public function getEmbeddedScheduleEntries(): array {
        return $this->scheduleEntries->getValues();
    }

    /**
     * @return ScheduleEntry[]
     */
    public function getScheduleEntries(): array {
        return $this->scheduleEntries->getValues();
    }

    public function addScheduleEntry(ScheduleEntry $scheduleEntry): self {
        if (!$this->scheduleEntries->contains($scheduleEntry)) {
            $this->scheduleEntries[] = $scheduleEntry;
            $scheduleEntry->activity = $this;
        }

        return $this;
    }

    public function removeScheduleEntry(ScheduleEntry $scheduleEntry): self {
        if ($this->scheduleEntries->removeElement($scheduleEntry)) {
            if ($scheduleEntry->activity === $this) {
                $scheduleEntry->activity = null;
            }
        }

        return $this;
    }

    /**
     * @return ActivityResponsible[]
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('activityResponsibles')]
    #[Groups(['Activity:ActivityResponsibles'])]
    public function getEmbeddedActivityResponsibles(): array {
        return $this->getActivityResponsibles();
    }

    /**
     * @return ActivityResponsible[]
     */
    public function getActivityResponsibles(): array {
        return $this->activityResponsibles->getValues();
    }

    public function addActivityResponsible(ActivityResponsible $activityResponsible): self {
        if (!$this->activityResponsibles->contains($activityResponsible)) {
            $this->activityResponsibles[] = $activityResponsible;
            $activityResponsible->activity = $this;
        }

        return $this;
    }

    public function removeActivityResponsible(ActivityResponsible $activityResponsible): self {
        if ($this->activityResponsibles->removeElement($activityResponsible)) {
            if ($activityResponsible->activity === $this) {
                $activityResponsible->activity = null;
            }
        }

        return $this;
    }
}
