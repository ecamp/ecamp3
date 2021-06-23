<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\InputFilter;
use App\Repository\CampRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
        'get' => ['security' => 'object.getOwner() == user or is_granted("ROLE_ADMIN")'],
        'patch' => ['security' => 'object.getOwner() == user or is_granted("ROLE_ADMIN")'],
        'delete' => ['security' => 'object.getOwner() == user or is_granted("ROLE_ADMIN")'],
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['owner'])]
class Camp extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\Column(type="string", length=32)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\NotBlank]
    #[ApiProperty(example: 'SoLa 2022')]
    private string $name;

    /**
     * @ORM\Column(type="text")
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ApiProperty(example: 'Abteilungs-Sommerlager 2022')]
    private string $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Piraten')]
    private ?string $motto;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Wiese hinter der alten Mühle')]
    private ?string $addressName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Schönriedweg 23')]
    private ?string $addressStreet;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: '1234')]
    private ?string $addressZipcode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\Length(max: 128)]
    #[ApiProperty(example: 'Hintertüpfingen')]
    private ?string $addressCity;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Assert\Type('bool')]
    #[Assert\DisableAutoMapping]
    #[ApiProperty(example: false, writable: false)]
    private bool $isPrototype;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    private ?string $campPrototypeId;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(writable: false)]
    private ?User $creator;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ownedCamps")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(writable: false)]
    private ?User $owner;

    /**
     * @ORM\OneToMany(targetEntity=Period::class, mappedBy="camp", orphanRemoval=true)
     */
    private $periods;

    /**
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="camp", orphanRemoval=true)
     */
    private $activities;

    public function __construct() {
        $this->periods = new ArrayCollection();
        $this->activities = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;

        return $this;
    }

    public function getMotto(): ?string {
        return $this->motto;
    }

    public function setMotto(?string $motto): self {
        $this->motto = $motto;

        return $this;
    }

    public function getAddressName(): ?string {
        return $this->addressName;
    }

    public function setAddressName(?string $addressName): self {
        $this->addressName = $addressName;

        return $this;
    }

    public function getAddressStreet(): ?string {
        return $this->addressStreet;
    }

    public function setAddressStreet(?string $addressStreet): self {
        $this->addressStreet = $addressStreet;

        return $this;
    }

    public function getAddressZipcode(): ?string {
        return $this->addressZipcode;
    }

    public function setAddressZipcode(?string $addressZipcode): self {
        $this->addressZipcode = $addressZipcode;

        return $this;
    }

    public function getAddressCity(): ?string {
        return $this->addressCity;
    }

    public function setAddressCity(?string $addressCity): self {
        $this->addressCity = $addressCity;

        return $this;
    }

    public function getIsPrototype(): ?bool {
        return $this->isPrototype;
    }

    public function setIsPrototype(bool $isPrototype): self {
        $this->isPrototype = $isPrototype;

        return $this;
    }

    public function getCampPrototypeId(): ?string {
        return $this->campPrototypeId;
    }

    public function setCampPrototypeId(?string $campPrototypeId): self {
        $this->campPrototypeId = $campPrototypeId;

        return $this;
    }

    public function getCreator(): ?User {
        return $this->creator;
    }

    public function setCreator(?User $creator): self {
        $this->creator = $creator;

        return $this;
    }

    public function getOwner(): ?User {
        return $this->owner;
    }

    public function setOwner(?User $owner): self {
        $this->owner = $owner;

        return $this;
    }

    public function getCamp(): ?Camp {
        return $this;
    }

    /**
     * @return Collection|Period[]
     */
    public function getPeriods(): Collection {
        return $this->periods;
    }

    public function addPeriod(Period $period): self {
        if (!$this->periods->contains($period)) {
            $this->periods[] = $period;
            $period->setCamp($this);
        }

        return $this;
    }

    public function removePeriod(Period $period): self {
        if ($this->periods->removeElement($period)) {
            // set the owning side to null (unless already changed)
            if ($period->getCamp() === $this) {
                $period->setCamp(null);
            }
        }

        return $this;
    }

    /**
     * @return Activity[]|Collection
     */
    public function getActivities(): Collection {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setCamp($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getCamp() === $this) {
                $activity->setCamp(null);
            }
        }

        return $this;
    }
}
