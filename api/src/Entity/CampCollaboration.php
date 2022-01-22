<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\CampCollaborationRepository;
use App\Validator\AllowTransition\AssertAllowTransitions;
use App\Validator\AssertEitherIsNull;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A user participating in some way in the planning or realization of a camp.
 *
 * @ORM\Entity(repositoryClass=CampCollaborationRepository::class)
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="inviteKey_unique", columns={"inviteKey"}),
 *     @ORM\UniqueConstraint(name="user_camp_unique", fields={"user", "camp"}),
 *     @ORM\UniqueConstraint(name="inviteEmail_camp_unique", fields={"inviteEmail", "camp"})
 * })
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_fully_authenticated()'],
        'post' => [
            'denormalization_context' => [
                'groups' => ['write', 'create'],
            ],
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'openapi_context' => [
                'description' => 'Also sends an invitation email to the inviteEmail address, if specified.',
            ],
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)',
        ],
        'patch' => [
            'denormalization_context' => ['groups' => ['write', 'update']],
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security' => '(user === object.user) or is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ['Default', 'update'],
        ],
        'delete' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
        self::RESEND_INVITATION => [
            'security' => '(user === object.user) or is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'method' => 'PATCH',
            'path' => 'camp_collaborations/{id}/'.self::RESEND_INVITATION,
            'denormalization_context' => [
                'groups' => ['resend_invitation'],
            ],
            'openapi_context' => [
                'summary' => 'Send the invitation email for this CampCollaboration again. Only possible, if the status is already '.self::STATUS_INVITED.'.',
            ],
            'validation_groups' => ['Default', 'resend_invitation'],
        ],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['camp', 'activityResponsibles.activity'])]
#[UniqueEntity(
    fields: ['user', 'camp'],
    message: 'This user is already present in the camp.',
    ignoreNull: true
)]
#[UniqueEntity(
    fields: ['inviteEmail', 'camp'],
    message: 'This inviteEmail is already present in the camp.',
    ignoreNull: true
)]
class CampCollaboration extends BaseEntity implements BelongsToCampInterface {
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => ['read', 'CampCollaboration:Camp', 'CampCollaboration:User'],
        'swagger_definition_name' => 'read',
    ];
    public const RESEND_INVITATION = 'resend_invitation';

    public const ROLE_GUEST = 'guest';
    public const ROLE_MEMBER = 'member';
    public const ROLE_MANAGER = 'manager';

    public const VALID_ROLES = [
        self::ROLE_GUEST,
        self::ROLE_MEMBER,
        self::ROLE_MANAGER,
    ];

    public const STATUS_INVITED = 'invited';
    public const STATUS_ESTABLISHED = 'established';
    public const STATUS_INACTIVE = 'inactive';

    public const VALID_STATUS = [
        self::STATUS_INVITED,
        self::STATUS_ESTABLISHED,
        self::STATUS_INACTIVE,
    ];

    /**
     * List of whole-day responsibilities that the collaborator has in the camp.
     *
     * @ORM\OneToMany(targetEntity="DayResponsible", mappedBy="campCollaboration", orphanRemoval=true)
     */
    #[ApiProperty(writable: false, example: '["/day_responsibles/1a2b3c4d"]')]
    public Collection $dayResponsibles;

    /**
     * List of activities in the camp that the collaborator is responsible for planning or carrying out.
     *
     * @ORM\OneToMany(targetEntity="ActivityResponsible", mappedBy="campCollaboration", orphanRemoval=true)
     */
    #[ApiProperty(writable: false, example: '["/activity_responsibles/1a2b3c4d"]')]
    public Collection $activityResponsibles;

    /**
     * The receiver email address of the invitation email, in case the collaboration does not yet have
     * a user account. Either this field or the user field should be null.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[AssertEitherIsNull(other: 'user')]
    #[ApiProperty(example: 'some-email@example.com')]
    #[Groups(['read', 'create'])]
    public ?string $inviteEmail = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    #[ApiProperty(readable: false, writable: false)]
    public ?string $inviteKey = null;

    /**
     * The person that is collaborating in the camp. Cannot be changed once the campCollaboration is established.
     * Either this field or the inviteEmail field should be null.
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="collaborations")
     * @ORM\JoinColumn(nullable=true, onDelete="cascade")
     */
    #[AssertEitherIsNull(other: 'inviteEmail')]
    #[ApiProperty(example: '/users/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    public ?User $user = null;

    /**
     * The camp that the collaborator is part of. Cannot be changed once the campCollaboration is created.
     *
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="collaborations")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[Assert\Valid]
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    public ?Camp $camp = null;

    /**
     * Indicates whether the collaborator is still invited, has left the camp, or is participating normally.
     * Cannot be set when creating a campCollaboration, but can be updated depending on the current status
     * and the updater's access rights.
     *
     * The status ESTABLISHED can only be reached via the /invitations endpoint.
     *
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    #[Assert\Choice(choices: self::VALID_STATUS)]
    #[Assert\EqualTo(value: self::STATUS_INVITED, groups: ['resend_invitation'])]
    #[Assert\EqualTo(value: self::STATUS_INACTIVE, groups: ['delete'])]
    #[AssertAllowTransitions(
        [
            ['from' => self::STATUS_INVITED, 'to' => [self::STATUS_INACTIVE]],
            ['from' => self::STATUS_INACTIVE, 'to' => [self::STATUS_INVITED]],
            ['from' => self::STATUS_ESTABLISHED, 'to' => [self::STATUS_INACTIVE]],
        ],
        groups: ['update']
    )]
    #[ApiProperty(default: self::STATUS_INVITED, example: self::STATUS_INACTIVE)]
    #[Groups(['read', 'update'])]
    public string $status = self::STATUS_INVITED;

    /**
     * The role that this person has in the camp. Depending on the role, the collaborator might have
     * different access rights. There must always be at least one manager in a camp.
     *
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    #[Assert\Choice(choices: self::VALID_ROLES)]
    #[ApiProperty(example: self::ROLE_MEMBER)]
    #[Groups(['read', 'write'])]
    public string $role;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[ApiProperty(readable: false, writable: false)]
    public ?string $collaborationAcceptedBy = null;

    public function __construct() {
        $this->dayResponsibles = new ArrayCollection();
        $this->activityResponsibles = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    /**
     * @return Camp
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('camp')]
    #[Groups('CampCollaboration:Camp')]
    public function getEmbeddedCamp(): ?Camp {
        return $this->camp;
    }

    /**
     * @return User
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('user')]
    #[Groups('CampCollaboration:User')]
    public function getEmbeddedUser(): ?User {
        return $this->user;
    }

    #[ApiProperty(readable: false, writable: false)]
    public function isGuest(): bool {
        return self::ROLE_GUEST === $this->role;
    }

    #[ApiProperty(readable: false, writable: false)]
    public function isMember(): bool {
        return self::ROLE_MEMBER === $this->role;
    }

    #[ApiProperty(readable: false, writable: false)]
    public function isManager(): bool {
        return self::ROLE_MANAGER === $this->role;
    }

    /**
     * @return ActivityResponsible[]
     */
    public function getActivityResponsibles(): array {
        return $this->activityResponsibles->getValues();
    }

    public function addActivityResponsible(ActivityResponsible $activityResponsible): self {
        if (!$this->activityResponsibles->contains($activityResponsible)) {
            $this->activityResponsibles[] = $activityResponsible;
            $activityResponsible->campCollaboration = $this;
        }

        return $this;
    }

    public function removeActivityResponsible(ActivityResponsible $activityResponsible): self {
        if ($this->activityResponsibles->removeElement($activityResponsible)) {
            if ($activityResponsible->campCollaboration === $this) {
                $activityResponsible->campCollaboration = null;
            }
        }

        return $this;
    }

    /**
     * @return DayResponsible[]
     */
    public function getDayResponsibles(): array {
        return $this->dayResponsibles->getValues();
    }

    public function addDayResponsible(DayResponsible $dayResponsible): self {
        if (!$this->dayResponsibles->contains($dayResponsible)) {
            $this->dayResponsibles[] = $dayResponsible;
            $dayResponsible->campCollaboration = $this;
        }

        return $this;
    }

    public function removeDayResponsible(DayResponsible $dayResponsible): self {
        if ($this->dayResponsibles->removeElement($dayResponsible)) {
            if ($dayResponsible->campCollaboration === $this) {
                $dayResponsible->campCollaboration = null;
            }
        }

        return $this;
    }
}
