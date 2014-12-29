<?php

namespace EcampCore\Event;

use EcampCore\Entity\User;
use Zend\EventManager\Event;

class UserEvent extends Event {

    /**
     * @var User
     */
    protected $user;


    public function __construct($name, $target, User $user){
        parent::__construct($name, $target);

        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(){
        return $this->user;
    }

}