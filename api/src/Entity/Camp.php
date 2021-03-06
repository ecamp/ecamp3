<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\InputFilter;
use App\Repository\CampRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The main entity that eCamp is designed to manage. Contains programme which may be
 * distributed across multiple time periods.
 *
 * @ORM\Entity(repositoryClass=CampRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_fully_authenticated()'],
        'post' => [
            'security' => 'is_fully_authenticated()',
            'input_formats' => ['jsonld', 'jsonapi', 'json'],
            'validation_groups' => ['Default', 'camp:create'],
        ],
    ],
    itemOperations: [
        'get' => ['security' => 'object.owner == user or is_granted("ROLE_ADMIN")'],
        'patch' => [
            'security' => 'object.owner == user or is_granted("ROLE_ADMIN")',
            'denormalization_context' => [
                'groups' => ['camp:update'],
                'allow_extra_attributes' => false,
            ],
        ],
        'delete' => ['security' => 'object.owner == user or is_granted("ROLE_ADMIN")'],
    ]
)]
class Camp extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="camp", orphanRemoval=true)
     */
    #[SerializedName('campCollaborations')]
    public Collection $collaborations;

    /**
     * The time periods of the camp, there must be at least one. Periods in a camp may not overlap.
     * When creating a camp, the initial periods may be specified as nested payload, but updating,
     * adding or removing periods later should be done through the period endpoints.
     *
     * @ORM\OneToMany(targetEntity="Period", mappedBy="camp", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"start": "ASC"})
     */
    #[Assert\Valid]
    #[Assert\Count(min: 1, groups: ['camp:create'])]
    #[ApiProperty(writableLink: true, example: '[{ "description": "Hauptlager", "start": "2022-01-01", "end": "2022-01-08" }]')]
    #[Groups(['Default'])]
    public Collection $periods;

    /**
     * Types of programme, such as sports activities or meal times.
     *
     * @ORM\OneToMany(targetEntity="Category", mappedBy="camp", orphanRemoval=true)
     */
    #[ApiProperty(writable: false, example: '["/categories/1a2b3c4d"]')]
    public Collection $categories;

    /**
     * All the programme that will be carried out during the camp. An activity may be carried out
     * multiple times in the same camp.
     *
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="camp", orphanRemoval=true)
     */
    #[ApiProperty(writable: false, example: '["/activities/1a2b3c4d"]')]
    public Collection $activities;

    /**
     * Lists for collecting the required materials needed for carrying out the programme. Each collaborator
     * has a material list, and there may be more, such as shopping lists.
     *
     * @ORM\OneToMany(targetEntity="MaterialList", mappedBy="camp", orphanRemoval=true)
     */
    #[ApiProperty(writable: false, example: '["/material_lists/1a2b3c4d"]')]
    public Collection $materialLists;

    /**
     * The id of the camp that was used as a template for creating this camp. Internal for now, is
     * not published through the API.
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    public ?string $campPrototypeId = null;

    /**
     * Whether this camp may serve as a template for creating other camps.
     *
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('bool')]
    #[Assert\DisableAutoMapping]
    #[ApiProperty(example: false, writable: false)]
    public bool $isPrototype = false;

    /**
     * A short name for the camp.
     *
     * @ORM\Column(type="string", length=32)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\NotBlank]
    #[ApiProperty(example: 'SoLa 2022')]
    #[Groups(['Default', 'camp:update'])]
    public string $name;

    /**
     * The full title of the camp.
     *
     * @ORM\Column(type="text")
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ApiProperty(example: 'Abteilungs-Sommerlager 2022')]
    #[Groups(['Default', 'camp:update'])]
    public string $title;

    /**
     * The thematic topic (if any) of the camp's programme and storyline.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Piraten')]
    #[Groups(['Default', 'camp:update'])]
    public ?string $motto = null;

    /**
     * A textual description of the location of the camp.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Wiese hinter der alten Mühle')]
    #[Groups(['Default', 'camp:update'])]
    public ?string $addressName = null;

    /**
     * The street name and number (if any) of the location of the camp.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Schönriedweg 23')]
    #[Groups(['Default', 'camp:update'])]
    public ?string $addressStreet = null;

    /**
     * The zipcode of the location of the camp.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: '1234')]
    #[Groups(['Default', 'camp:update'])]
    public ?string $addressZipcode = null;

    /**
     * The name of the town where the camp will take place.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Hintertüpfingen')]
    #[Groups(['Default', 'camp:update'])]
    public ?string $addressCity = null;

    /**
     * The person that created the camp. This value never changes, even when the person
     * leaves the camp.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(writable: false)]
    public ?User $creator = null;

    /**
     * The single person currently in charge of managing the camp. If this person leaves
     * the camp, another collaborator must be appointed as owner.
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedCamps")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    public ?User $owner = null;

    public function __construct() {
        $this->collaborations = new ArrayCollection();
        $this->periods = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->materialLists = new ArrayCollection();
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
