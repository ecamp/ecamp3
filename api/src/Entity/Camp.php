<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\InputFilter;
use App\Repository\CampRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CampRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_fully_authenticated()'],
        'post' => [
            'security' => 'is_fully_authenticated()',
            'input_formats' => ['jsonld', 'jsonapi', 'json'],
        ],
    ],
    itemOperations: [
        'get' => ['security' => 'object.owner == user or is_granted("ROLE_ADMIN")'],
        'patch' => ['security' => 'object.owner == user or is_granted("ROLE_ADMIN")'],
        'delete' => ['security' => 'object.owner == user or is_granted("ROLE_ADMIN")'],
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['owner'])]
class Camp extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="camp", orphanRemoval=true)
     *
     * @var CampCollaboration[]
     */
    public $collaborations;

    /**
     * @ORM\OneToMany(targetEntity="Period", mappedBy="camp", orphanRemoval=true)
     * @ORM\OrderBy({"start": "ASC"})
     *
     * @var Period[]
     */
    public $periods;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="camp", orphanRemoval=true)
     *
     * @var Category[]
     */
    public $categories;

    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="camp", orphanRemoval=true)
     *
     * @var Activity[]
     */
    public $activities;

    /**
     * @ORM\OneToMany(targetEntity="MaterialList", mappedBy="camp", orphanRemoval=true)
     *
     * @var MaterialList[]
     */
    public $materialLists;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    public ?string $campPrototypeId;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('bool')]
    #[Assert\DisableAutoMapping]
    #[ApiProperty(example: false, writable: false)]
    public bool $isPrototype;

    /**
     * @ORM\Column(type="string", length=32)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\NotBlank]
    #[ApiProperty(example: 'SoLa 2022')]
    public string $name;

    /**
     * @ORM\Column(type="text")
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ApiProperty(example: 'Abteilungs-Sommerlager 2022')]
    public string $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Piraten')]
    public ?string $motto;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Wiese hinter der alten Mühle')]
    public ?string $addressName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Schönriedweg 23')]
    public ?string $addressStreet;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: '1234')]
    public ?string $addressZipcode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Hintertüpfingen')]
    public ?string $addressCity;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(writable: false)]
    public ?User $creator = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedCamps")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(writable: false)]
    public ?User $owner = null;

    public function __construct() {
        $this->collaborations = new ArrayCollection();
        $this->periods = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->materialLists = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this;
    }

    public function getCampCollaborations(): array {
        return $this->collaborations->getValues();
    }

    public function addCampCollaboration(CampCollaboration $collaboration): void {
        $collaboration->camp = $this;
        $this->collaborations->add($collaboration);
    }

    public function removeCampCollaboration(CampCollaboration $collaboration): void {
        $collaboration->camp = null;
        $this->collaborations->removeElement($collaboration);
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
     * @param string $userId
     */
    public function isCollaborator($userId): bool {
        if ($this?->creator->getId() == $userId) {
            return true;
        }
        if ($this?->owner->getId() == $userId) {
            return true;
        }

        return $this->cmpCollaborations->exists(function ($idx, CampCollaboration $cc) use ($userId) {
            return $cc->isEstablished() && $cc?->user->getId() == $userId;
        });
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

    public function getCategories(): array {
        return $this->categories->getValues();
    }

    public function addCategory(Category $category): void {
        $category->camp = $this;
        $this->categories->add($category);
    }

    public function removeCategory(Category $category): void {
        $category->camp = null;
        $this->categories->removeElement($category);
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

    public function getMaterialLists(): array {
        return $this->materialLists->getValues();
    }

    public function addMaterialList(MaterialList $materialList): void {
        $materialList->camp = $this;
        $this->materialLists->add($materialList);
    }

    public function removeMaterialList(MaterialList $materialList): void {
        $materialList->camp = null;
        $this->materialLists->removeElement($materialList);
    }
}
