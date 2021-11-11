<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\InputFilter\CleanHTML;
use App\InputFilter\Trim;
use App\Repository\CampRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The main entity that eCamp is designed to manage. Contains programme which may be
 * distributed across multiple time periods.
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_fully_authenticated()'],
        'post' => [
            'security' => 'is_fully_authenticated()',
            'input_formats' => ['jsonld', 'jsonapi', 'json'],
            'validation_groups' => ['Default', 'create'],
            'denormalization_context' => ['groups' => ['write', 'create']],
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)',
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
        ],
        'patch' => [
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'denormalization_context' => ['groups' => ['write', 'update']],
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
        ],
        'delete' => ['security' => 'object.owner == user'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[Entity(repositoryClass: CampRepository::class)]
class Camp extends BaseEntity implements BelongsToCampInterface {
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => ['read', 'Camp:Periods', 'Period:Days', 'Camp:CampCollaborations', 'CampCollaboration:User'],
        'swagger_definition_name' => 'read',
    ];

    #[SerializedName('campCollaborations')]
    #[Groups(['read'])]
    #[OneToMany(targetEntity: 'CampCollaboration', mappedBy: 'camp', orphanRemoval: true)]
    public Collection $collaborations;

    /**
     * The time periods of the camp, there must be at least one. Periods in a camp may not overlap.
     * When creating a camp, the initial periods may be specified as nested payload, but updating,
     * adding or removing periods later should be done through the period endpoints.
     */
    #[Assert\Valid]
    #[Assert\Count(min: 1, groups: ['create'])]
    #[ApiProperty(
        writableLink: true,
        example: '[{ "description": "Hauptlager", "start": "2022-01-01", "end": "2022-01-08" }]',
    )]
    #[Groups(['read', 'create'])]
    #[OneToMany(targetEntity: 'Period', mappedBy: 'camp', orphanRemoval: true, cascade: ['persist'])]
    #[OrderBy(value: ['start' => 'ASC'])]
    public Collection $periods;

    /**
     * Types of programme, such as sports activities or meal times.
     */
    #[ApiProperty(writable: false, example: '["/categories/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[OneToMany(targetEntity: 'Category', mappedBy: 'camp', orphanRemoval: true)]
    public Collection $categories;

    /**
     * All the programme that will be carried out during the camp. An activity may be carried out
     * multiple times in the same camp.
     */
    #[ApiProperty(writable: false, example: '["/activities/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[OneToMany(targetEntity: 'Activity', mappedBy: 'camp', orphanRemoval: true)]
    public Collection $activities;

    /**
     * Lists for collecting the required materials needed for carrying out the programme. Each collaborator
     * has a material list, and there may be more, such as shopping lists.
     */
    #[ApiProperty(writable: false, example: '["/material_lists/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[OneToMany(targetEntity: 'MaterialList', mappedBy: 'camp', orphanRemoval: true)]
    public Collection $materialLists;

    /**
     * The id of the camp that was used as a template for creating this camp. Internal for now, is
     * not published through the API.
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    #[Column(type: 'string', length: 16, nullable: true)]
    public ?string $campPrototypeId = null;

    /**
     * Whether this camp may serve as a template for creating other camps.
     */
    #[Assert\Type('bool')]
    #[Assert\DisableAutoMapping]
    #[ApiProperty(example: true, writable: false)]
    #[Groups(['read'])]
    #[Column(type: 'boolean')]
    public bool $isPrototype = false;

    /**
     * A short name for the camp.
     */
    #[Trim]
    #[CleanHTML]
    #[Assert\NotBlank]
    #[ApiProperty(example: 'SoLa 2022')]
    #[Groups(['read', 'write'])]
    #[Column(type: 'string', length: 32)]
    public string $name;

    /**
     * The full title of the camp.
     */
    #[Trim]
    #[CleanHTML]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ApiProperty(example: 'Abteilungs-Sommerlager 2022')]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text')]
    public string $title;

    /**
     * The thematic topic (if any) of the camp's programme and storyline.
     */
    #[Trim]
    #[CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Piraten')]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: true)]
    public ?string $motto = null;

    /**
     * A textual description of the location of the camp.
     */
    #[Trim]
    #[CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Wiese hinter der alten Mühle')]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: true)]
    public ?string $addressName = null;

    /**
     * The street name and number (if any) of the location of the camp.
     */
    #[Trim]
    #[CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Schönriedweg 23')]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: true)]
    public ?string $addressStreet = null;

    /**
     * The zipcode of the location of the camp.
     */
    #[Trim]
    #[CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: '1234')]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: true)]
    public ?string $addressZipcode = null;

    /**
     * The name of the town where the camp will take place.
     */
    #[Trim]
    #[CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Hintertüpfingen')]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: true)]
    public ?string $addressCity = null;

    /**
     * The person that created the camp. This value never changes, even when the person
     * leaves the camp.
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(writable: false)]
    #[Groups(['read'])]
    #[ManyToOne(targetEntity: 'User')]
    #[JoinColumn(nullable: false)]
    public ?User $creator = null;

    /**
     * The single person currently in charge of managing the camp. If this person leaves
     * the camp, another collaborator must be appointed as owner.
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    #[ManyToOne(targetEntity: 'User', inversedBy: 'ownedCamps')]
    #[JoinColumn(nullable: false)]
    public ?User $owner = null;

    public function __construct() {
        $this->collaborations = new ArrayCollection();
        $this->periods = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->materialLists = new ArrayCollection();
    }

    /**
     * @return Period[]
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('periods')]
    #[Groups(['Camp:Periods'])]
    public function getEmbeddedPeriods(): array {
        return $this->periods->getValues();
    }

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this;
    }

    /**
     * The people working on planning and carrying out the camp. Only collaborators have access
     * to the camp's contents.
     *
     * @return CampCollaboration[]
     */
    #[ApiProperty(writable: false, example: '["/camp_collaborations/1a2b3c4d"]')]
    public function getCampCollaborations(): array {
        return $this->collaborations->getValues();
    }

    /**
     * The people working on planning and carrying out the camp. Only collaborators have access
     * to the camp's contents.
     *
     * @return CampCollaboration[]
     */
    #[ApiProperty(writable: false, readableLink: true)]
    #[SerializedName('campCollaborations')]
    #[Groups('Camp:CampCollaborations')]
    public function getEmbeddedCampCollaborations(): array {
        return $this->collaborations->getValues();
    }

    public function addCampCollaboration(CampCollaboration $collaboration): self {
        if (!$this->collaborations->contains($collaboration)) {
            $this->collaborations[] = $collaboration;
            $collaboration->camp = $this;
        }

        return $this;
    }

    public function removeCampCollaboration(CampCollaboration $collaboration): self {
        if ($this->collaborations->removeElement($collaboration)) {
            if ($collaboration->camp === $this) {
                $collaboration->camp = null;
            }
        }

        return $this;
    }

    public function getRole($userId): string {
        if ($this?->owner->getId() === $userId) {
            return CampCollaboration::ROLE_MANAGER;
        }

        $campCollaborations = $this->collaborations->filter(function (CampCollaboration $cc) use ($userId) {
            return $cc?->user->getId() == $userId;
        });

        if (1 == $campCollaborations->count()) {
            /** @var CampCollaboration $campCollaboration */
            $campCollaboration = $campCollaborations->first();
            if ($campCollaboration->isEstablished()) {
                return $campCollaboration->role;
            }
        }

        return CampCollaboration::ROLE_GUEST;
    }

    /**
     * @return Period[]
     */
    public function getPeriods(): array {
        return $this->periods->getValues();
    }

    public function addPeriod(Period $period): self {
        if (!$this->periods->contains($period)) {
            $this->periods[] = $period;
            $period->camp = $this;
        }

        return $this;
    }

    public function removePeriod(Period $period): self {
        if ($this->periods->removeElement($period)) {
            if ($period->camp === $this) {
                $period->camp = null;
            }
        }

        return $this;
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array {
        return $this->categories->getValues();
    }

    public function addCategory(Category $category): self {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->camp = $this;
        }

        return $this;
    }

    public function removeCategory(Category $category): self {
        if ($this->categories->removeElement($category)) {
            if ($category->camp === $this) {
                $category->camp = null;
            }
        }

        return $this;
    }

    /**
     * @return Activity[]
     */
    public function getActivities(): array {
        return $this->activities->getValues();
    }

    public function addActivity(Activity $activity): self {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->camp = $this;
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self {
        if ($this->activities->removeElement($activity)) {
            if ($activity->camp === $this) {
                $activity->camp = null;
            }
        }

        return $this;
    }

    /**
     * @return MaterialList[]
     */
    public function getMaterialLists(): array {
        return $this->materialLists->getValues();
    }

    public function addMaterialList(MaterialList $materialList): self {
        if (!$this->materialLists->contains($materialList)) {
            $this->materialLists[] = $materialList;
            $materialList->camp = $this;
        }

        return $this;
    }

    public function removeMaterialList(MaterialList $materialList): self {
        if ($this->materialLists->removeElement($materialList)) {
            if ($materialList->camp === $this) {
                $materialList->camp = null;
            }
        }

        return $this;
    }
}
