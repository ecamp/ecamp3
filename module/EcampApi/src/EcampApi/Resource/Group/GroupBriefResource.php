<?php
namespace EcampApi\Resource\Group;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\Group;

class GroupBriefResource extends HalResource
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

        $this->getLinks()->add($selfLink)
                        ->add($webLink);
    }
}
