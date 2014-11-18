<?php
namespace EcampApi\Resource\EventCategory;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\EventCategory as EventCategory;
use EcampApi\Resource\Camp\CampBriefResource;

class EventCategoryDetailResource extends HalResource
{
    public function __construct(EventCategory $entity)
    {
        $object = array(
            'id'		=> 	$entity->getId(),
            'name'		=>	$entity->getName(),
            'short'     =>  $entity->getShort(),
            'color'		=>	$entity->getColor(),
            'numbering'	=>	$entity->getNumberingStyle(),
            'camp'		=>	new CampBriefResource($entity->getCamp()),
        );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/event_categories', array('event_category' => $entity->getId()));

        $this->getLinks()->add($selfLink);

    }
}
