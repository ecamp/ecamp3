<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ActivityResponsibleRepository;
use App\Validator\AssertBelongsToSameCamp;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A person that is responsible for planning or carrying out an activity.
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_fully_authenticated()'],
        'post' => ['security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
        'delete' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[UniqueEntity(fields: ['activity', 'campCollaboration'])]
#[ApiFilter(SearchFilter::class, properties: ['activity', 'activity.camp'])]
#[Entity(repositoryClass: ActivityResponsibleRepository::class)]
#[UniqueConstraint(name: 'activity_campCollaboration_unique', columns: ['activityId', 'campCollaborationId'])]
class ActivityResponsible extends BaseEntity implements BelongsToCampInterface {
    /**
     * The activity that the person is responsible for.
     */
    #[Assert\NotNull]
    #[ApiProperty(example: '/activities/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    #[ManyToOne(targetEntity: 'Activity', inversedBy: 'activityResponsibles')]
    #[JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Activity $activity = null;

    /**
     * The person that is responsible. Must be a collaborator in the same camp as the activity.
     */
    #[ApiProperty(example: '/camp_collaborations/1a2b3c4d')]
    #[AssertBelongsToSameCamp]
    #[Groups(['read', 'write'])]
    #[ManyToOne(targetEntity: 'CampCollaboration', inversedBy: 'activityResponsibles')]
    #[JoinColumn(nullable: false)]
    public ?CampCollaboration $campCollaboration = null;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->activity?->camp;
    }
}
