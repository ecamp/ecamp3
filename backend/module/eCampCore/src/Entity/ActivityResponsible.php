<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ActivityResponsible extends BaseEntity implements BelongsToCampInterface {
    /**
     * @var Activity
     * @ORM\ManyToOne(targetEntity="Activity")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $activity;

    /**
     * @var CampCollaboration
     * @ORM\ManyToOne(targetEntity="CampCollaboration")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campCollaboration;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return Activity
     */
    public function getActivity() {
        return $this->activity;
    }

    public function setActivity($activity) {
        $this->activity = $activity;
    }

    /**
     * @return Camp
     */
    public function getCamp() {
        return $this->activity->getCamp();
    }

    /**
     * @return CampCollaboration
     */
    public function getCampCollaboration() {
        return $this->campCollaboration;
    }

    public function setCampCollaboration(CampCollaboration $collaboration) {
        $this->campCollaboration = $collaboration;
    }
}
