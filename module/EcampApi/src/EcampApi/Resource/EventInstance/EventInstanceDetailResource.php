<?php
namespace EcampApi\Resource\EventInstance;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\EventInstance as EventInstance;
use EcampApi\Resource\Event\EventBriefResource;
use EcampApi\Resource\Period\PeriodBriefResource;

class EventInstanceDetailResource extends HalResource
{
    public function __construct(EventInstance $entity)
    {
        $object = array(
            'id'		=> 	$entity->getId(),
            'periodId'  =>  $entity->getPeriod()->getId(),
            'start'		=> 	$entity->getStartTime()->format(\DateTime::ISO8601),
            'start_min' =>  $entity->getOffsetInMinutes(),
            'end'		=> 	$entity->getEndTime()->format(\DateTime::ISO8601),
            'end_min'   =>  $entity->getOffsetInMinutes() + $entity->getDurationInMinutes(),
            'left'      =>  $entity->getLeftOffset(),
            'width'     =>  $entity->getWidth(),
            'event'		=> 	new EventBriefResource($entity->getEvent()),
            'period'	=> 	new PeriodBriefResource($entity->getPeriod())
        );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/event_instances', array('event_instance' => $entity->getId()));

        $this->getLinks()->add($selfLink);

    }
}
