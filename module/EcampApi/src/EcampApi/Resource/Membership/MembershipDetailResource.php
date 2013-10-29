<?php
namespace EcampApi\Resource\Membership;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\GroupMembership as Membership;
use EcampApi\Resource\User\UserBriefResource;
use EcampApi\Resource\Group\GroupBriefResource;

class MembershipDetailResource extends HalResource
{
    public function __construct(Membership $entity)
    {
        $object = array(
                'id'		=> 	$entity->getId(),
                'user'		=>	new UserBriefResource($entity->getUser()),
                'group'		=>	new GroupBriefResource($entity->getGroup()),
                'role'		=>	$entity->getRole(),
                'status'	=>  $entity->getStatus()
                );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/memberships', array('membership' => $entity->getId()));

        $this->getLinks()->add($selfLink);
    }
}
