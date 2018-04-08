<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_relationships", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="from_to_unique",columns={"from_id","to_id"})
 * })
 */
class UserRelationship extends BaseEntity
{
    const TYPE_FRIEND  = 1;

    public function __construct()
    {
        parent::__construct();

        $this->type = self::TYPE_FRIEND;
    }


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $from;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $to;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @var UserRelationship
     * @ORM\OneToOne(targetEntity="UserRelationship")
     * @ORM\JoinColumn(name="counterpart", referencedColumnName="id")
     */
    private $counterpart;


    /**
     * @return User
     */
    public function getFrom(): User
    {
        return $this->from;
    }

    public function setFrom(User $from): void
    {
        $this->from = $from;
    }


    /**
     * @return User
     */
    public function getTo(): User
    {
        return $this->to;
    }

    public function setTo(User $to): void
    {
        $this->to = $to;
    }


    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }


    /**
     * @return UserRelationship
     */
    public function getCounterpart(): UserRelationship
    {
        return $this->counterpart;
    }

    public function setCounterpart(UserRelationship $counterpart): void
    {
        if ($this->counterpart !== $counterpart) {
            $this->counterpart = $counterpart;

            if ($this->counterpart != null) {
                $this->counterpart->setCounterpart($this);
            }
        }
    }
}
