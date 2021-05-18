<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
#[UniqueEntity('email')]
#[UniqueEntity('username')]
#[ApiResource(
    collectionOperations: [
        'get' => [],
        'post' => [
            'security' => 'true', // allow unauthenticated clients to create (register) users
            'input_formats' => [ 'jsonld', 'jsonapi', 'json' ]
        ]
    ],
    attributes: ['security' => 'is_granted("ROLE_ADMIN") or object == user']
)]
class User extends BaseEntity implements UserInterface {
    /**
     * Unique email of the user.
     *
     * @ORM\Column(type="string", length=64, nullable=false, unique=true)
     */
    #[ApiProperty(example: 'bi-pi@example.com')]
    public ?string $email = null;

    /**
     * Unique username, lower alphanumeric symbols and underscores only.
     *
     * @ORM\Column(type="string", length=32, nullable=false, unique=true)
     */
    #[ApiProperty(example: 'bipi')]
    public ?string $username;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[ApiProperty(example: 'Robert')]
    public ?string $firstname = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[ApiProperty(example: 'Baden-Powell')]
    public ?string $surname = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[ApiProperty(example: 'Bi-Pi')]
    public ?string $nickname = null;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    #[ApiProperty(example: 'en')]
    public ?string $language = null;

    /**
     * @ORM\Column(type="json")
     */
    #[ApiProperty(writable: false)]
    private array $roles = [];

    /**
     * The hashed password
     * @ORM\Column(type="string", length=255)
     */
    #[ApiProperty(readable: false, example: 'learning-by-doing-101')]
    private ?string $password;

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
        // $this->plainPassword = null;
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
}
