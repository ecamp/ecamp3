<?php
namespace EcampApi\Resource\Period;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\Period as Period;

class PeriodBriefResource extends HalResource
{
    public function __construct(Period $entity)
    {
        $object = array(
                'id'				=> 	$entity->getId(),
                'start'				=> 	$entity->getStart()->format(\DateTime::ISO8601),
                'end'				=> 	$entity->getEnd()->format(\DateTime::ISO8601),
                'numDays'			=> 	$entity->getNumberOfDays(),
                );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/periods', array('period' => $entity->getId()));

        $this->getLinks()->add($selfLink);

    }
}
