<?php
namespace EcampApi\Resource\Membership;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\GroupMembership as Membership;

class MembershipBriefResource extends HalResource
{
    public function __construct(Membership $entity)
    {
        $object = array(
                'id'		=> 	$entity->getId(),
                'user'		=>	$entity->getUser()->getId(),
                'group'		=>	$entity->getGroup()->getId(),
                'role'		=>	$entity->getRole(),
                'status'	=>  $entity->getStatus()
                );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/memberships', array('membership' => $entity->getId()));

        $this->getLinks()->add($selfLink);
    }
}
