<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Mtarld\SymbokBundle\Annotation\Getter;
use Mtarld\SymbokBundle\Annotation\Setter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`",uniqueConstraints={@ORM\UniqueConstraint(columns={"email"}),@ORM\UniqueConstraint(columns={"username"})})
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
#[ApiResource]
class User extends BaseEntity
{
    const STATE_NONREGISTERED = 'non-registered';
    const STATE_REGISTERED = 'registered';
    const STATE_ACTIVATED = 'activated';
    const STATE_DELETED = 'deleted';

    const ROLE_GUEST = 'guest';
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    /**
     * Unique email of the user.
     *
     * @ORM\Column(type="text", nullable=true)
     * @Getter
     * @Setter
     */
    public ?string $email = null;

    /**
     * Unique username, lower alphanumeric symbols and underscores only.
     *
     * @ORM\Column(type="string", length=32, nullable=true, unique=true)
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
     * @ORM\Column(type="string", length=16, nullable=false)
     * @Getter
     * @Setter
     */
    public string $role;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Getter
     * @Setter
     */
    public ?string $language = null;

    public function __construct() {
        $this->role = self::ROLE_GUEST;
    }

    /**
     * Displayable name of the user.
     */
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
}
