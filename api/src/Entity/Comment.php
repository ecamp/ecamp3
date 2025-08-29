<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\InputFilter;
use App\Repository\CommentRepository;
use App\State\CommentCreateProcessor;
use App\Validator\AssertBelongsToSameCamp;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A Comment someone left on an activity, to give feedback on the planned programme,
 * for notes which are only relevant during camp planning, or for other communication.
 */
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or
                       object.author === user',
        ),
        new Delete(
            security: 'object.author === user',
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Post(
            processor: CommentCreateProcessor::class,
            denormalizationContext: ['groups' => ['create', 'write']],
            securityPostDenormalize: 'is_granted("CAMP_COLLABORATOR", object)',
        ),
        new GetCollection(
            uriTemplate: self::ACTIVITY_SUBRESOURCE_URI_TEMPLATE,
            uriVariables: [
                'activityId' => new Link(
                    toProperty: 'activity',
                    fromClass: Activity::class,
                    security: 'is_granted("CAMP_COLLABORATOR", activity) or
                               is_granted("CAMP_IS_PUBLIC", activity)',
                ),
            ],
            security: 'is_fully_authenticated()',
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    order: ['createTime' => 'ASC'],
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['camp', 'activity'])]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment extends BaseEntity implements BelongsToCampInterface {
    public const ACTIVITY_SUBRESOURCE_URI_TEMPLATE = '/activities/{activityId}/comments{._format}';

    /**
     * The camp this comment belongs to.
     */
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Camp $camp = null;

    /**
     * The activity this comment belongs to.
     */
    #[AssertBelongsToSameCamp]
    #[ApiProperty(example: '/activities/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Activity $activity = null;

    /**
     * The author of the comment.
     */
    #[Assert\DisableAutoMapping] // avoids validation error when author is null in payload
    #[ApiProperty(example: '/users/1a2b3c4d', writable: false)]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?User $author = null;

    /**
     * The actual comment.
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanHTML]
    #[Assert\NotBlank]
    #[Assert\Length(max: 1024)]
    #[ApiProperty(example: 'This activity is great!')]
    #[Groups(['read', 'create'])]
    #[ORM\Column(type: 'text', nullable: false)]
    public ?string $textHtml = null;

    /**
     * Persisted description of the context where the comment was originally writen.
     * Only non-null when activity pointer is null, i.e. activity was deleted.
     * Currently defined as the title of the activity when it was deleted.
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\Length(max: 32)]
    #[ApiProperty(example: 'Sportolympiade', writable: false)]
    #[Groups(['read', 'create'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $orphanDescription = null;

    public function __construct() {
        parent::__construct();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    #[ApiProperty(writable: false)]
    #[Groups(['read'])]
    public function getCreateTime(): \DateTime {
        return $this->createTime;
    }
}
