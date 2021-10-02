<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\InputFilter;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A person using eCamp.
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_fully_authenticated()'],
        'post' => [
            'security' => 'true', // allow unauthenticated clients to create (register) users
            'input_formats' => ['jsonld', 'jsonapi', 'json'],
            'validation_groups' => ['Default', 'create'],
            'denormalization_context' => ['groups' => ['write', 'create']],
        ],
    ],
    itemOperations: [
        self::ACTIVATE => [
            'method' => 'PATCH',
            'path' => 'users/{id}/activate.{_format}',
            'denormalization_context' => ['groups' => ['activate']],
        ],
        'get' => ['security' => 'object == user'],
        'patch' => [
            'security' => 'object == user',
        ],
        'delete' => ['security' => 'false'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface {
    public const ACTIVATE = 'activate';

    public const STATE_NONREGISTERED = 'non-registered';
    public const STATE_REGISTERED = 'registered';
    public const STATE_ACTIVATED = 'activated';
    public const STATE_DELETED = 'deleted';

    /**
     * The camps that this user is the owner of.
     *
     * @ORM\OneToMany(targetEntity="Camp", mappedBy="owner")
     */
    #[ApiProperty(readable: false, writable: false)]
    public Collection $ownedCamps;

    /**
     * All the camps that this user participates in.
     *
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="user", orphanRemoval=true)
     */
    #[ApiProperty(readable: false, writable: false)]
    public Collection $collaborations;

    /**
     * Unique email of the user.
     *
     * @ORM\Column(type="string", length=64, nullable=false, unique=true)
     */
    #[InputFilter\Trim]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[ApiProperty(example: 'bi-pi@example.com')]
    #[Groups(['read', 'write'])]
    public ?string $email = null;

    /**
     * Unique username. Lower case alphanumeric symbols, dashes, periods and underscores only.
     *
     * @ORM\Column(type="string", length=32, nullable=false, unique=true)
     */
    #[InputFilter\Trim]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[a-z0-9_.-]+$/')]
    #[ApiProperty(example: 'bipi')]
    #[Groups(['read', 'create'])]
    public ?string $username = null;

    /**
     * The user's (optional) first name.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[ApiProperty(example: 'Robert')]
    #[Groups(['read', 'write'])]
    public ?string $firstname = null;

    /**
     * The user's (optional) last name.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[ApiProperty(example: 'Baden-Powell')]
    #[Groups(['read', 'write'])]
    public ?string $surname = null;

    /**
     * The user's (optional) nickname or scout name.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[ApiProperty(example: 'Bi-Pi')]
    #[Groups(['read', 'write'])]
    public ?string $nickname = null;

    /**
     * The optional preferred language of the user, as an ICU language code.
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    #[InputFilter\Trim]
    #[Assert\Locale]
    #[ApiProperty(example: 'en')]
    #[Groups(['read', 'write'])]
    public ?string $language = null;

    /**
     * The state of this user.
     *
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    public string $state = self::STATE_NONREGISTERED;

    /**
     * User-Input for activation.
     */
    #[ApiProperty(readable: false, writable: true)]
    #[Groups(['activate'])]
    public ?string $activationKey = null;

    /**
     * InvitationKey for new user.
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    public ?string $activationKeyHash = null;

    /**
     * The technical roles that this person has in the eCamp application.
     *
     * @ORM\Column(type="json")
     */
    #[ApiProperty(writable: false)]
    public array $roles = ['ROLE_USER'];

    /**
     * The hashed password. Of course not exposed through the API.
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    public ?string $password = null;

    /**
     * A new password for this user. At least 8 characters.
     */
    #[SerializedName('password')]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 8)]
    #[ApiProperty(readable: false, writable: true, example: 'learning-by-doing-101')]
    #[Groups(['read', 'write'])]
    public ?string $plainPassword = null;

    public function __construct() {
        $this->ownedCamps = new ArrayCollection();
        $this->collaborations = new ArrayCollection();
    }

    /**
     * A displayable name of the user.
     *
     * @return null|string
     */
    #[ApiProperty(example: 'Robert Baden-Powell')]
    #[Groups(['read'])]
    public function getDisplayName(): ?string {
        if (!empty($this->nickname)) {
            return $this->nickname;
        }
        if (!empty($this->firstname)) {
            if (!empty($this->surname)) {
                return $this->firstname.' '.$this->surname;
            }

            return $this->firstname;
        }

        return $this->username;
    }

    /**
     * Returning a salt is only needed if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    #[ApiProperty(readable: false, writable: false)]
    public function getSalt(): ?string {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function getUserIdentifier(): ?string {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function ownsCamps(): bool {
        return (bool) (count($this->getOwnedCamps()));
    }

    /**
     * @return Camp[]
     */
    public function getOwnedCamps(): array {
        return $this->ownedCamps->getValues();
    }

    public function addOwnedCamp(Camp $ownedCamp): self {
        if (!$this->ownedCamps->contains($ownedCamp)) {
            $this->ownedCamps[] = $ownedCamp;
            $ownedCamp->owner = $this;
        }

        return $this;
    }

    public function removeOwnedCamp(Camp $ownedCamp): self {
        if ($this->ownedCamps->removeElement($ownedCamp)) {
            if ($ownedCamp->owner === $this) {
                $ownedCamp->owner = null;
            }
        }

        return $this;
    }

    /**
     * @return CampCollaboration[]
     */
    #[ApiProperty(readable: false, writable: false)]
    public function getCampCollaborations(): array {
        return $this->collaborations->getValues();
    }

    public function addCampCollaboration(CampCollaboration $collaboration): self {
        if (!$this->collaborations->contains($collaboration)) {
            $this->collaborations[] = $collaboration;
            $collaboration->user = $this;
        }

        return $this;
    }

    public function removeCampCollaboration(CampCollaboration $collaboration): self {
        if ($this->collaborations->removeElement($collaboration)) {
            if ($collaboration->user === $this) {
                $collaboration->user = null;
            }
        }

        return $this;
    }
}
