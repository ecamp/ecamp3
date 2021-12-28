<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
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
 * The properties available for all other eCamp users are here.
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'false'],
        'post' => [
            'security' => 'true', // allow unauthenticated clients to create (register) users
            'input_formats' => ['jsonld', 'jsonapi', 'json'],
            'validation_groups' => ['Default', 'create'],
            'normalization_context' => ['groups' => ['read', 'User:create']],
            'denormalization_context' => ['groups' => ['write', 'create']],
        ],
    ],
    itemOperations: [
        self::ACTIVATE => [
            'method' => 'PATCH',
            'path' => 'users/{id}/activate.{_format}',
            'denormalization_context' => ['groups' => ['activate']],
        ],
        'get' => ['security' => 'is_fully_authenticated()'],
        'patch' => [
            'security' => 'object === user',
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
     * The state of this user.
     *
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    #[ApiProperty(readable: false, writable: false)]
    public string $state = self::STATE_NONREGISTERED;

    /**
     * User-Input for activation.
     */
    #[ApiProperty(readable: false, writable: true)]
    #[Groups(['activate'])]
    public ?string $activationKey = null;

    /**
     * InvitationKey hashed for new user.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    public ?string $activationKeyHash = null;

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
    #[Groups(['write'])]
    public ?string $plainPassword = null;

    /**
     * @ORM\OneToOne(targetEntity="Profile", inversedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, unique=true, onDelete="restrict")
     */
    #[Assert\Valid]
    #[Assert\NotNull(groups: ['create'])]
    #[ApiProperty(
        writableLink: true,
        example: [
            'email' => Profile::EXAMPLE_EMAIL,
            'username' => Profile::EXAMPLE_USERNAME,
            'firstname' => Profile::EXAMPLE_FIRSTNAME,
            'surname' => Profile::EXAMPLE_SURNAME,
            'nickname' => Profile::EXAMPLE_NICKNAME,
            'language' => Profile::EXAMPLE_LANGUAGE,
        ]
    )]
    #[Groups(['create'])]
    public Profile $profile;

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
        return $this->profile->getDisplayName();
    }

    #[ApiProperty]
    #[SerializedName('profile')]
    #[Groups(['read'])]
    public function getProfile(): Profile {
        return $this->profile;
    }

    #[ApiProperty(readableLink: true)]
    #[SerializedName('profile')]
    #[Groups(['User:create'])]
    public function getEmbeddedProfile(): Profile {
        return $this->profile;
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
        return $this->profile->username;
    }

    public function getEmail(): ?string {
        return $this->profile->email;
    }

    public function getUserIdentifier(): ?string {
        return $this->profile->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->profile->roles;
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
