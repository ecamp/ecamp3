<?php
namespace EcampApi\Resource\User;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\User;

class UserDetailResource extends HalResource
{
    public function __construct(User $user)
    {
        $object = array(
                'id' 			=>  $user->getId(),
                'username'		=>	$user->getUsername(),
                'email'			=>	$user->getEmail(),
                'scoutname'		=>	$user->getScoutname(),
                'firstname'		=>	$user->getFirstname(),
                'surname'		=>	$user->getSurname(),
                'fullname'  	=>	$user->getFullName(),
                'displayName' 	=>  $user->getDisplayName()
            );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/users', array('user' => $user->getId()));

        $campsLink = new Link('camps');
        $campsLink->setRoute('api/users/camps', array('user' => $user->getId()));

        $collabLink = new Link('collaborations');
        $collabLink->setRoute('api/users/collaborations', array('user' => $user->getId()));

        $collabLink = new Link('memberships');
        $collabLink->setRoute('api/users/memberships', array('user' => $user->getId()));

        $eventRespLink = new Link('event_resps');
        $eventRespLink->setRoute('api/users/event_resps', array('user' => $user->getId()));

        $this->getLinks()->add($selfLink)
                         ->add($campsLink)
                         ->add($collabLink)
                         ->add($eventRespLink);
    }
}
