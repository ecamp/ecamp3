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
 *
 * @ORM\Entity(repositoryClass=ActivityRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_authenticated()'],
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
class Activity extends AbstractContentNodeOwner implements BelongsToCampInterface {
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => [
            'read',
            'Activity:Category',
            'Activity:CampCollaborations',
            'Activity:ScheduleEntries',
            'Activity:ContentNodes',
        ],
        'swagger_definition_name' => 'read',
    ];

    /**
     * The list of points in time when this activity's programme will be carried out.
     *
     * @ORM\OneToMany(targetEntity="ScheduleEntry", mappedBy="activity", orphanRemoval=true, cascade={"persist"})
     */
    #[Assert\Valid]
    #[Assert\Count(min: 1, groups: ['create'])]
    #[ApiProperty(
        writableLink: true,
        example: '[{ "period": "/periods/1a2b3c4a", "length": 100, "periodOffset": 1000 }]',
    )]
    #[Groups(['read', 'create'])]
    public Collection $scheduleEntries;

    /**
     * The list of people that are responsible for planning or carrying out this activity.
     *
     * @ORM\OneToMany(targetEntity="ActivityResponsible", mappedBy="activity", orphanRemoval=true)
     */
    #[ApiProperty(readable: false, writable: false)]
    public Collection $activityResponsibles;

    /**
     * The camp to which this activity belongs.
     *
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="activities")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[Assert\DisableAutoMapping] // camp is set in the DataPersister
    #[ApiProperty(writable: false, example: '/camps/1a2b3c4d')]
    #[Groups(['read'])]
    public ?Camp $camp = null;

    /**
     * The category to which this activity belongs. The category determines color and numbering scheme
     * of the activity, and is used for marking similar activities. Must be in the same camp as the activity.
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="activities")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ApiProperty(example: '/categories/1a2b3c4d')]
    #[AssertBelongsToSameCamp(groups: ['update'])]
    #[Groups(['read', 'write'])]
    public ?Category $category = null;

    /**
     * The title of this activity that is shown in the picasso.
     *
     * @ORM\Column(type="text")
     */
    #[ApiProperty(example: 'Sportolympiade')]
    #[Groups(['read', 'write'])]
    public ?string $title = null;

    /**
     * The physical location where this activity's programme will be carried out.
     *
     * @ORM\Column(type="text")
     */
    #[ApiProperty(example: 'Spielwiese')]
    #[Groups(['read', 'write'])]
    public string $location = '';

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
     * @return CampCollaboration[]
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('campCollaborations')]
    #[Groups(['Activity:CampCollaborations'])]
    public function getEmbeddedCampCollaborations(): array {
        return $this->getCampCollaborations();
    }

    /**
     * The list of people that are responsible for planning or carrying out this activity.
     *
     * @return CampCollaboration[]
     */
    #[ApiProperty(writable: false, example: '["/camp_collaborations/1a2b3c4d"]')]
    #[RelatedCollectionLink(CampCollaboration::class, ['activityResponsibles.activity' => '$this'])]
    #[Groups(['read'])]
    public function getCampCollaborations(): array {
        return array_filter(
            $this
                ->activityResponsibles
                ->map(fn (ActivityResponsible $activityResponsible) => $activityResponsible->campCollaboration)
                ->getValues(),
            fn ($cc) => !is_null($cc)
        );
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
