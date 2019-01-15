<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="camp_collaborations", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="user_camp_unique", columns={"user_id","camp_id"})
 * })
 */
class CampCollaboration extends BaseEntity {
    const ROLE_GUEST   = 'guest';
    const ROLE_MEMBER  = 'member';
    const ROLE_MANAGER = 'manager';

    const STATUS_UNRELATED      = 'unrelated';
    const STATUS_REQUESTED 		= 'requested';
    const STATUS_INVITED 		= 'invited';
    const STATUS_ESTABLISHED 	= 'established';


    public function __construct() {
        parent::__construct();

        $this->status = self::STATUS_UNRELATED;
        $this->role = self::ROLE_GUEST;
    }


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false)
     */
    private $camp;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $role;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $collaborationAcceptedBy;


    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): void {
        $this->user = $user;
    }


    /**
     * @return Camp
     */
    public function getCamp() {
        return $this->camp;
    }

    public function setCamp($camp) {
        $this->camp = $camp;
    }


    /**
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }
    /**
     * @param string $status
     * @throws \Exception
     */
    public function setStatus(string $status): void {
        if (!in_array($status, [self::STATUS_INVITED, self::STATUS_REQUESTED, self::STATUS_ESTABLISHED])) {
            throw new \Exception('Invalid status: ' . $status);
        }
        $this->status = $status;
    }

    public function isEstablished(): bool {
        return ($this->status === self::STATUS_ESTABLISHED);
    }

    public function isRequest(): bool {
        return ($this->status === self::STATUS_REQUESTED);
    }

    public function isInvitation(): bool {
        return ($this->status === self::STATUS_INVITED);
    }


    /**
     * @return string
     */
    public function getRole(): string {
        return $this->role;
    }
    /**
     * @param string $role
     * @throws \Exception
     */
    public function setRole(string $role): void {
        if (!in_array($role, [self::ROLE_GUEST, self::ROLE_MEMBER, self::ROLE_MANAGER])) {
            throw new \Exception('Invalid role: ' . $role);
        }
        $this->role = $role;
    }

    public function isGuest(): bool {
        return ($this->role === self::ROLE_GUEST);
    }

    public function isMember(): bool {
        return ($this->role === self::ROLE_MEMBER);
    }

    public function isManager(): bool {
        return ($this->role === self::ROLE_MANAGER);
    }


    /**
     * @return string
     */
    public function getCollaborationAcceptedBy() {
        return $this->collaborationAcceptedBy;
    }

    public function setCollaborationAcceptedBy($collaborationAcceptedBy) {
        $this->collaborationAcceptedBy = $collaborationAcceptedBy;
    }


    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function PrePersist() {
        parent::PrePersist();

        if (in_array($this->status, [self::STATUS_REQUESTED, self::STATUS_UNRELATED])) {
            $this->collaborationAcceptedBy = null;
        }
    }

    /** @ORM\PreUpdate */
    public function PreUpdate() {
        parent::PreUpdate();
    }
}
