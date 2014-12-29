<?php
namespace EcampApi\Resource\Event;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\Event as Event;
use EcampApi\Resource\EventCategory\EventCategoryBriefResource;

class EventBriefResource extends HalResource
{
    public function __construct(Event $entity)
    {
        $object = array(
            'id'			=> 	$entity->getId(),
            'categoryId'    =>  $entity->getEventCategory()->getId(),
            'title'			=> 	$entity->getTitle(),
            'category'		=>  new EventCategoryBriefResource($entity->getEventCategory())
        );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/events', array('event' => $entity->getId()));

        $webLink = new Link('web');
        $webLink->setRoute('web/camp/default', array(
            'camp' => $entity->getCamp(),
            'controller' => 'event',
            'action' => 'index'
        ), array(
            'query' => array( 'eventId' => $entity->getId())
        ));

        $this->getLinks()
            ->add($selfLink)
            ->add($webLink)
        ;

    }
}
