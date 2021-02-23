<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="activity_campCollaboration_unique", columns={"activityId", "campCollaborationId"})
 * })
 */
class ActivityResponsible extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\ManyToOne(targetEntity="Activity")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Activity $activity = null;

    /**
     * @ORM\ManyToOne(targetEntity="CampCollaboration")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?CampCollaboration $campCollaboration = null;

    public function getActivity(): ?Activity {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): void {
        $this->activity = $activity;
    }

    public function getCamp(): ?Camp {
        return (null != $this->activity) ? $this->activity->getCamp() : null;
    }

    public function getCampCollaboration(): ?CampCollaboration {
        return $this->campCollaboration;
    }

    public function setCampCollaboration(?CampCollaboration $collaboration): void {
        $this->campCollaboration = $collaboration;
    }
}
