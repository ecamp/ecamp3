<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Validator\AssertBelongsToSameCamp;
use Doctrine\ORM\Mapping as ORM;

/**
 * A person that is responsible for planning or carrying out an activity.
 *
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="activity_campCollaboration_unique", columns={"activityId", "campCollaborationId"})
 * })
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'delete'],
)]
class ActivityResponsible extends BaseEntity implements BelongsToCampInterface {
    /**
     * The activity that the person is responsible for.
     *
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="activityResponsibles")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[ApiProperty(example: '/activities/1a2b3c4d')]
    public ?Activity $activity = null;

    /**
     * The person that is responsible. Must be a collaborator in the same camp as the activity.
     *
     * @ORM\ManyToOne(targetEntity="CampCollaboration", inversedBy="activityResponsibles")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ApiProperty(example: '/camp_collaborations/1a2b3c4d')]
    #[AssertBelongsToSameCamp]
    public ?CampCollaboration $campCollaboration = null;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->activity?->camp;
    }
}
