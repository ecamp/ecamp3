<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\InputFilter;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
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
        ],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("ROLE_ADMIN") or object == user'],
        'patch' => ['security' => 'is_granted("ROLE_ADMIN") or object == user'],
        'delete' => ['security' => 'is_granted("ROLE_ADMIN")'],
    ]
)]
class User extends BaseEntity implements UserInterface {
    /**
     * Unique email of the user.
     *
     * @ORM\Column(type="string", length=64, nullable=false, unique=true)
     */
    #[InputFilter\Trim]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[ApiProperty(example: 'bi-pi@example.com')]
    private ?string $email = null;

    /**
     * Unique username. Lower case alphanumeric symbols, dashes, periods and underscores only.
     *
     * @ORM\Column(type="string", length=32, nullable=false, unique=true)
     */
    #[InputFilter\Trim]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[a-z0-9_.-]+$/')]
    #[ApiProperty(example: 'bipi')]
    private ?string $username;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[ApiProperty(example: 'Robert')]
    private ?string $firstname = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[ApiProperty(example: 'Baden-Powell')]
    private ?string $surname = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[ApiProperty(example: 'Bi-Pi')]
    private ?string $nickname = null;

    /**
     * The preferred language of the user, as an ICU language code.
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    #[InputFilter\Trim]
    #[Assert\Locale]
    #[ApiProperty(example: 'en')]
    private ?string $language = null;

    /**
     * @ORM\Column(type="json")
     */
    #[ApiProperty(writable: false)]
    private array $roles = [];

    /**
     * The hashed password.
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(readable: false, writable: false)]
    private ?string $password = null;

    /**
     * The new password for this user. At least 8 characters.
     */
    #[SerializedName('password')]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 8)]
    #[ApiProperty(readable: false, writable: true, example: 'learning-by-doing-101')]
    private ?string $plainPassword = null;

    /**
     * @ORM\OneToMany(targetEntity=Camp::class, mappedBy="owner")
     */
    #[ApiProperty(writable: false, readableLink: false, writableLink: false)]
    private Collection $ownedCamps;

    public function __construct() {
        $this->ownedCamps = new ArrayCollection();
    }

    /**
     * A displayable name of the user.
     *
     * @return null|string
     */
    #[ApiProperty(example: 'Robert Baden-Powell')]
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
     * Returning a salt is only needed, if you are not using a modern
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
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function setUsername(string $username): self {
        $this->username = $username;

        return $this;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self {
        $this->firstname = $firstname;

        return $this;
    }

    public function getSurname(): ?string {
        return $this->surname;
    }

    public function setSurname(string $surname): self {
        $this->surname = $surname;

        return $this;
    }

    public function getNickname(): ?string {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self {
        $this->nickname = $nickname;

        return $this;
    }

    public function getLanguage(): ?string {
        return $this->language;
    }

    public function setLanguage(?string $language): self {
        $this->language = $language;

        return $this;
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

    public function setRoles(array $roles = []): self {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(?string $password): self {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self {
        $this->plainPassword = $plainPassword;

        return $this;
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
            $ownedCamp->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedCamp(Camp $ownedCamp): self {
        if ($this->ownedCamps->removeElement($ownedCamp)) {
            // set the owning side to null (unless already changed)
            if ($ownedCamp->getOwner() === $this) {
                $ownedCamp->setOwner(null);
            }
        }

        return $this;
    }
}
