<?php
namespace EcampApi\Resource\Day;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\Day as Day;
use EcampApi\Resource\Period\PeriodBriefResource;

class DayDetailResource extends HalResource
{
    public function __construct(Day $entity)
    {
        $object = array(
            'id'		=> 	$entity->getId(),
            'periodId'  =>  $entity->getPeriod()->getId(),
            'period'	=> 	new PeriodBriefResource($entity->getPeriod()),
            'offset'	=>  $entity->getDayOffset(),
            'date'		=> 	$entity->getStart()->format(\DateTime::ISO8601),
            'notes'		=> 	$entity->getNotes()
        );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/days', array('day' => $entity->getId()));

        $eventInstanceLink = new Link('event_instances');
        $eventInstanceLink->setRoute('api/days/event_instances', array('day' => $entity->getId()));

        $this->getLinks()->add($selfLink)
                        ->add($eventInstanceLink);

    }
}
