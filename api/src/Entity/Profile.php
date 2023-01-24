<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\InputFilter;
use App\Repository\ProfileRepository;
use App\State\ProfileUpdateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The profile of a person using eCamp.
 * The properties available to related eCamp users are here.
 * Related means that they were or are collaborators in the same camp.
 */
#[ApiResource(
    operations: [
        new Get(
            security: 'is_authenticated()'
        ),
        new Patch(
            processor: ProfileUpdateProcessor::class,
            denormalizationContext: ['groups' => ['write', 'update']],
            security: 'object.user === user'
        ),
        new GetCollection(
            security: 'false'
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']]
)]
#[ORM\Entity(repositoryClass: ProfileRepository::class)]
#[ORM\Table(name: '`profile`')]
class Profile extends BaseEntity {
    public const EXAMPLE_EMAIL = 'bi-pi@example.com';
    public const EXAMPLE_FIRSTNAME = 'Robert';
    public const EXAMPLE_SURNAME = 'Baden-Powell';
    public const EXAMPLE_NICKNAME = 'Bi-Pi';
    public const EXAMPLE_LANGUAGE = 'en';

    /**
     * Unique email of the user.
     * Can only be changed by setting the newEmail field, which triggers an email verification flow.
     */
    #[InputFilter\Trim]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[ApiProperty(example: self::EXAMPLE_EMAIL)]
    #[Groups(['read', 'create'])]
    #[ORM\Column(type: 'string', length: 64, nullable: false, unique: true)]
    public ?string $email = null;

    /**
     * New email.
     * If set, a verification email is sent to this email address.
     */
    #[InputFilter\Trim]
    #[Assert\Email]
    #[ApiProperty(example: self::EXAMPLE_EMAIL)]
    #[Groups(['write'])]
    public ?string $newEmail = null;

    /**
     * Untrusted email.
     * Will become the email when it is verified by a link sent to the address.
     */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    public ?string $untrustedEmail = null;

    /**
     * User input for email verification.
     */
    #[ApiProperty(readable: false, writable: true)]
    #[Groups(['update'])]
    public ?string $untrustedEmailKey = null;

    /**
     * The hashed untrusted-email-key. Of course not exposed through the API.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $untrustedEmailKeyHash = null;

    /**
     * Google id of the user.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $googleId = null;

    /**
     * PBS MiData id of the user.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $pbsmidataId = null;

    /**
     * CeviDB id of the user.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $cevidbId = null;

    /**
     * JublaDB id of the user.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $jubladbId = null;

    /**
     * The user's (optional) first name.
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\Length(max: 64)]
    #[ApiProperty(example: self::EXAMPLE_FIRSTNAME)]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $firstname = null;

    /**
     * The user's (optional) last name.
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\Length(max: 64)]
    #[ApiProperty(example: self::EXAMPLE_SURNAME)]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $surname = null;

    /**
     * The user's (optional) nickname or scout name.
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\Length(max: 32)]
    #[ApiProperty(example: self::EXAMPLE_NICKNAME)]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $nickname = null;

    /**
     * The optional preferred language of the user, as an ICU language code.
     */
    #[InputFilter\Trim]
    #[ApiProperty(example: self::EXAMPLE_LANGUAGE)]
    #[Assert\Choice(['en', 'en-CH-scout', 'de', 'de-CH-scout', 'fr', 'fr-CH-scout', 'it', 'it-CH-scout'])]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    public ?string $language = null;

    /**
     * The technical roles that this person has in the eCamp application.
     */
    #[ApiProperty(writable: false)]
    #[ORM\Column(type: 'json')]
    public array $roles = ['ROLE_USER'];

    #[ApiProperty(writable: false, example: '/users/1a2b3c4d')]
    #[Groups(['read'])]
    #[ORM\OneToOne(targetEntity: User::class, mappedBy: 'profile')]
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

        return 'Noname-'.substr(md5($this->email), 0, 4);
    }
}
