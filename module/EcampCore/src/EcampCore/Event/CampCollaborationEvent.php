<?php

namespace EcampCore\Event;

use EcampCore\Entity\CampCollaboration;
use Zend\EventManager\Event;

class CampCollaborationEvent extends Event {

    /**
     * @var CampCollaboration
     */
    protected $campCollaboration;

    public function __construct($name, $target, CampCollaboration $campCollaboration){
        parent::__construct($name, $target);

        $this->campCollaboration = $campCollaboration;
    }

    /**
     * @return CampCollaboration
     */
    public function getCampCollaboration(){
        return $this->campCollaboration;
    }

}