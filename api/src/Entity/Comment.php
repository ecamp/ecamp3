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
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\InputFilter;
use App\Repository\CommentRepository;
use App\State\CommentCreateProcessor;
use App\State\CommentUpdateProcessor;
use App\Validator\AssertBelongsToSameCamp;
use App\Validator\Comment\AssertSameAnchorAsParent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A Comment someone left on an activity, to give feedback on the planned programme,
 * for notes which are only relevant during camp planning, or for other communication.
 */
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object)',
        ),
        new Patch(
            denormalizationContext: ['groups' => ['update']],
            security: 'is_granted("CAMP_COLLABORATOR", object)',
            processor: CommentUpdateProcessor::class,
        ),
        new Delete(
            security: 'object.author !== null and object.author.user === user',
            validationContext: ['groups' => ['delete']],
            validate: true,
        ),
        new GetCollection(
            security: 'is_authenticated()',
            extraProperties: [
                'scoping_filters' => ['camp', 'activity'],
            ]
        ),
        new Post(
            denormalizationContext: ['groups' => ['create', 'write']],
            securityPostDenormalize: 'is_granted("CAMP_COLLABORATOR", object)',
            processor: CommentCreateProcessor::class,
        ),
        new GetCollection(
            uriTemplate: self::ACTIVITY_SUBRESOURCE_URI_TEMPLATE,
            uriVariables: [
                'activityId' => new Link(
                    toProperty: 'activity',
                    fromClass: Activity::class,
                    security: 'is_granted("CAMP_COLLABORATOR", activity)',
                ),
            ],
            security: 'is_fully_authenticated()',
        ),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    order: ['createTime' => \SortDirection::Ascending],
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['camp', 'activity'])]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment extends BaseEntity implements BelongsToCampInterface {
    public const ACTIVITY_SUBRESOURCE_URI_TEMPLATE = '/activities/{activityId}/comments{._format}';

    /**
     * What a comment can be about. A reply always shares these with the root of its thread.
     */
    public const ANCHOR_FIELDS = ['activity'];

    /**
     * The camp this comment belongs to. Can be left out on a reply, which always belongs to
     * the camp of the comment it replies to.
     */
    #[Assert\DisableAutoMapping] // a reply takes its camp from its parent, see getCamp()
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Camp $camp = null;

    /**
     * The activity this comment belongs to. If the comment is a reply, this is automatically
     * inherited from the replied comment.
     */
    #[AssertBelongsToSameCamp]
    #[ApiProperty(example: '/activities/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Activity $activity = null;

    /**
     * The comment this one replies to, or null if it starts a thread. Threads are flat, so
     * a reply always points at the root comment of its thread.
     */
    #[AssertBelongsToSameCamp]
    #[AssertSameAnchorAsParent]
    #[Assert\Expression('value === null or value.parent === null', 'Cannot reply to a reply.')]
    #[ApiProperty(example: '/comments/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Comment $parent = null;

    /**
     * The replies to this comment, oldest first. Always empty on a reply. Deleting a comment
     * also deletes its replies.
     *
     * @var Collection<int, Comment>
     */
    #[Assert\Expression(
        '!this.hasRepliesByOthers()',
        'It\'s not possible to delete a comment as long as other people have replied to it.',
        groups: ['delete']
    )]
    #[ApiProperty(writable: false, example: '["/comments/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent', cascade: ['remove'])]
    #[ORM\OrderBy(['createTime' => \SortDirection::Ascending])]
    public Collection $children;

    /**
     * The camp collaboration of the person who wrote the comment. Null once that person is
     * removed completely from the camp.
     */
    #[Assert\DisableAutoMapping] // avoids validation error when author is null in payload
    #[ApiProperty(writable: false, example: '/camp_collaborations/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: CampCollaboration::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'set null')]
    public ?CampCollaboration $author = null;

    /**
     * The display name of the author, saved when they were removed completely from the
     * camp. Null as long as the author is still part of the camp.
     */
    #[ApiProperty(writable: false, example: 'Bi-Pi')]
    #[Groups(['read'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $authorDisplayName = null;

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
     * Persisted description of the context where the comment was originally written. Only
     * non-null when activity pointer is null, i.e. activity was deleted. Currently defined
     * as the category shortname plus title of the activity when it was deleted.
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\Length(max: 32)]
    #[ApiProperty(writable: false, example: 'LS Sportolympiade')]
    #[Groups(['read', 'create'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $orphanDescription = null;

    /**
     * When the thread was resolved. Null if the comment is a reply or if the thread is
     * still open.
     */
    #[ApiProperty(writable: false, example: '2022-01-01T00:00:00+00:00')]
    #[Groups(['read'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTimeInterface $resolvedAt = null;

    /**
     * The camp collaboration of the person who resolved the thread. Null if the comment is
     * a reply, if the thread is still open, or if that person was removed from the camp.
     */
    #[Assert\DisableAutoMapping]
    #[ApiProperty(writable: false, example: '/camp_collaborations/1a2b3c4d')]
    #[Groups(['read'])]
    #[ORM\ManyToOne(targetEntity: CampCollaboration::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'set null')]
    public ?CampCollaboration $resolvedBy = null;

    /**
     * Write-only flag for resolving (true) or reopening (false) a thread. Only the root
     * comment of a thread can be resolved, not an individual reply.
     */
    #[Assert\Expression(
        'this.resolved === null or this.parent === null',
        message: 'Only the root comment of a thread can be resolved.',
    )]
    #[ApiProperty(readable: false, example: true)]
    #[Groups(['update'])]
    public ?bool $resolved = null;

    public function __construct() {
        parent::__construct();
        $this->children = new ArrayCollection();
    }

    #[Assert\NotNull]
    public function getCamp(): ?Camp {
        return $this->camp ?? $this->parent?->getCamp();
    }

    public function hasRepliesByOthers(): bool {
        foreach ($this->children as $reply) {
            if ($reply->author?->getId() !== $this->author?->getId() || null === $this->author) {
                return true;
            }
        }

        return false;
    }

    #[ApiProperty(writable: false)]
    #[Groups(['read'])]
    public function getCreateTime(): \DateTime {
        return $this->createTime;
    }
}
