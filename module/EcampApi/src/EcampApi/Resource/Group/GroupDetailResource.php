<?php
namespace EcampApi\Resource\Group;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\Group;

class GroupDetailResource extends HalResource
{
    public function __construct(Group $group)
    {
        $object = array(
                'id' 			=>  $group->getId(),
                'name'			=> 	$group->getName(),
                'description'	=> 	$group->getDescription(),
            );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/groups', array('group' => $group->getId()));

        $webLink = new Link('web');
        $webLink->setRoute('web/group-prefix/name', array('group' => $group->getId()));

        $campsLink = new Link('camps');
        $campsLink->setRoute('api/groups/camps', array('group' => $group->getId()));

        $membershipsLink = new Link('memberships');
        $membershipsLink->setRoute('api/groups/memberships', array('group' => $group->getId()));

        $groupsLink = new Link('groups');
        $groupsLink->setRoute('api/groups/groups', array('group' => $group->getId()));

        $this->getLinks()->add($selfLink)
                         ->add($webLink)
                         ->add($campsLink)
                         ->add($membershipsLink)
                         ->add($groupsLink);
    }
}
