<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\InputFilter;
use App\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The profile of a person using eCamp.
 * The properties available to related eCamp users are here.
 * Related means that they were or are collaborators in the same camp.
 *
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 * @ORM\Table(name="`profile`")
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'false'],
    ],
    itemOperations: [
        'get' => ['security' => 'is_fully_authenticated()'],
        'patch' => ['security' => 'object.user === user'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
class Profile extends BaseEntity {
    public const EXAMPLE_EMAIL = 'bi-pi@example.com';
    public const EXAMPLE_USERNAME = 'bipi';
    public const EXAMPLE_FIRSTNAME = 'Robert';
    public const EXAMPLE_SURNAME = 'Baden-Powell';
    public const EXAMPLE_NICKNAME = 'Bi-Pi';
    public const EXAMPLE_LANGUAGE = 'en';

    /**
     * Unique email of the user.
     *
     * @ORM\Column(type="string", length=64, nullable=false, unique=true)
     */
    #[InputFilter\Trim]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[ApiProperty(example: self::EXAMPLE_EMAIL)]
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
    #[ApiProperty(example: self::EXAMPLE_USERNAME)]
    #[Groups(['read', 'create'])]
    public string $username = '';

    /**
     * The user's (optional) first name.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[ApiProperty(example: self::EXAMPLE_FIRSTNAME)]
    #[Groups(['read', 'write'])]
    public ?string $firstname = null;

    /**
     * The user's (optional) last name.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[ApiProperty(example: self::EXAMPLE_SURNAME)]
    #[Groups(['read', 'write'])]
    public ?string $surname = null;

    /**
     * The user's (optional) nickname or scout name.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[ApiProperty(example: self::EXAMPLE_NICKNAME)]
    #[Groups(['read', 'write'])]
    public ?string $nickname = null;

    /**
     * The optional preferred language of the user, as an ICU language code.
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    #[InputFilter\Trim]
    #[ApiProperty(example: self::EXAMPLE_LANGUAGE)]
    #[Assert\Choice(['en', 'en-CH-scout', 'de', 'de-CH-scout', 'fr', 'fr-CH-scout', 'it', 'it-CH-scout'])]
    #[Groups(['read', 'write'])]
    public ?string $language = null;

    /**
     * The technical roles that this person has in the eCamp application.
     *
     * @ORM\Column(type="json")
     */
    #[ApiProperty(writable: false)]
    public array $roles = ['ROLE_USER'];

    #[ApiProperty(writable: false, example: '/users/1a2b3c4d')]
    #[Groups(['read'])]
    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="profile")
     */
    public User $user;

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
