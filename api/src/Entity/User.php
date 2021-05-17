<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Mtarld\SymbokBundle\Annotation\Getter;
use Mtarld\SymbokBundle\Annotation\Setter;
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
        'post' => ['security' => 'true'] // allow unauthenticated clients to create (register) users
    ],
    attributes: ['security' => 'is_granted("ROLE_ADMIN") or object == user']
)]
class User extends BaseEntity implements UserInterface {
    /**
     * Unique email of the user.
     *
     * @ORM\Column(type="text", nullable=false, unique=true)
     * @Getter
     * @Setter
     */
    public ?string $email = null;

    /**
     * Unique username, lower alphanumeric symbols and underscores only.
     *
     * @ORM\Column(type="string", length=32, nullable=false, unique=true)
     * @Getter
     * @Setter
     */
    public ?string $username;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Getter
     * @Setter
     */
    public ?string $firstname = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Getter
     * @Setter
     */
    public ?string $surname = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Getter
     * @Setter
     */
    public ?string $nickname = null;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Getter
     * @Setter
     */
    public ?string $language = null;

    /**
     * @ORM\Column(type="json")
     * @Setter
     */
    private array $roles = [];

    /**
     * The hashed password
     * @ORM\Column(type="string")
     * @Getter
     * @Setter
     */
    private string $password;

    /**
     * @see UserInterface
     */
    public function getUsername(): string {
        return (string)$this->username;
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

    public function setRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string {
        return (string)$this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
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
}
