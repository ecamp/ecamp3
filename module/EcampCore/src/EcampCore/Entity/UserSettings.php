<?php

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;
use EcampLib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_settings")
 */
class UserSettings extends BaseEntity
{
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $sendCampInvitations;



    public function setSendCampInvitations($value){
        $this->sendCampInvitations = $value;
    }

    /**
     * @return bool
     */
    public function getSendCampInvitations(){
        return $this->sendCampInvitations ?: true;
    }

}