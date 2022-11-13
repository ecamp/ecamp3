<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use App\State\UserActivateProcessor;
use App\State\UserCreateProcessor;
use App\State\UserUpdateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A person using eCamp.
 * The properties available for all other eCamp users are here.
 */
#[ApiResource(
    operations: [
        new Patch(
            processor: UserActivateProcessor::class,
            uriTemplate: 'users/{id}/activate{._format}',
            denormalizationContext: ['groups' => ['activate']]
        ),
        new Get(
            security: 'is_authenticated()'
        ),
        new Patch(
            processor: UserUpdateProcessor::class,
            security: 'object === user'
        ),
        new Delete(
            security: 'false'
        ),
        new GetCollection(
            security: 'false'
        ),
        new Post(
            processor: UserCreateProcessor::class,
            security: 'true', // allow unauthenticated clients to create (register) users
            inputFormats: ['jsonld', 'jsonapi', 'json'],
            validationContext: ['groups' => ['Default', 'create']],
            normalizationContext: ['groups' => ['read', 'User:create']],
            denormalizationContext: ['groups' => ['write', 'create']]
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']]
)]
#[UniqueEntity(
    fields: ['profile'],
    message: 'Only one User can reference a Profile.',
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface {
    public const ACTIVATE = 'activate';

    public const STATE_NONREGISTERED = 'non-registered';
    public const STATE_REGISTERED = 'registered';
    public const STATE_ACTIVATED = 'activated';
    public const STATE_DELETED = 'deleted';

    /**
     * The camps that this user is the owner of.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\OneToMany(targetEntity: Camp::class, mappedBy: 'owner')]
    public Collection $ownedCamps;

    /**
     * All the camps that this user participates in.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\OneToMany(targetEntity: CampCollaboration::class, mappedBy: 'user', orphanRemoval: true)]
    public Collection $collaborations;

    /**
     * The state of this user.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\Column(type: 'string', length: 16, nullable: false)]
    public string $state = self::STATE_NONREGISTERED;

    /**
     * ReCaptchaToken used on Register-View.
     */
    #[ApiProperty(readable: false, writable: true)]
    #[Groups(['create'])]
    public ?string $recaptchaToken = null;

    /**
     * User-Input for activation.
     */
    #[ApiProperty(readable: false, writable: true)]
    #[Groups(['activate'])]
    public ?string $activationKey = null;

    /**
     * InvitationKey hashed for new user.
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $activationKeyHash = null;

    /**
     * The hashed password. Of course not exposed through the API.
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $password = null;

    /**
     * A new password for this user. At least 12 characters, as is explicitly recommended by OWASP:
     * https://github.com/OWASP/ASVS/blob/master/4.0/en/0x11-V2-Authentication.md#v21-password-security
     * 2.1.1: Verify that user set passwords are at least 12 characters in length (after multiple spaces are combined).
     */
    #[SerializedName('password')]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 12, max: 128)]
    #[ApiProperty(readable: false, writable: true, example: 'learning-by-doing-101')]
    #[Groups(['write'])]
    public ?string $plainPassword = null;

    /**
     * The hashed password-reset-key. Of course not exposed through the API.
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $passwordResetKeyHash = null;

    #[Assert\Valid]
    #[Assert\NotNull(groups: ['create'])]
    #[ApiProperty(
        writableLink: true,
        example: [
            'email' => Profile::EXAMPLE_EMAIL,
            'firstname' => Profile::EXAMPLE_FIRSTNAME,
            'surname' => Profile::EXAMPLE_SURNAME,
            'nickname' => Profile::EXAMPLE_NICKNAME,
            'language' => Profile::EXAMPLE_LANGUAGE,
        ]
    )]
    #[Groups(['create'])]
    #[ORM\OneToOne(targetEntity: Profile::class, inversedBy: 'user', cascade: ['persist'], fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, unique: true, onDelete: 'restrict')]
    public Profile $profile;

    public function __construct() {
        parent::__construct();
        $this->ownedCamps = new ArrayCollection();
        $this->collaborations = new ArrayCollection();
    }

    /**
     * A displayable name of the user.
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

    public function getEmail(): ?string {
        return $this->profile->email;
    }

    public function getUserIdentifier(): string {
        return $this->profile->email;
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
        return (bool) count($this->getOwnedCamps());
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
