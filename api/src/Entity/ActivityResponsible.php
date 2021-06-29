<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="activity_campCollaboration_unique", columns={"activityId", "campCollaborationId"})
 * })
 */
#[ApiResource]
class ActivityResponsible extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="activityResponsibles")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?Activity $activity = null;

    /**
     * @ORM\ManyToOne(targetEntity="CampCollaboration", inversedBy="activityResponsibles")
     * @ORM\JoinColumn(nullable=false)
     */
    public ?CampCollaboration $campCollaboration = null;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->activity?->camp;
    }
}
