<?php
namespace EcampApi\Resource\Day;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\Day as Day;

class DayBriefResource extends HalResource
{
    public function __construct(Day $entity)
    {
        $object = array(
                'id'		=> 	$entity->getId(),
                'offset'	=>  $entity->getDayOffset(),
                'date'		=> 	$entity->getStart()->format(\DateTime::ISO8601)
                );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/days', array('day' => $entity->getId()));

        $this->getLinks()->add($selfLink);

    }
}
