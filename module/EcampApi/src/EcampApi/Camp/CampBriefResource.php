<?php
namespace EcampApi\Camp;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\Camp;

class CampBriefResource extends HalResource
{
    public function __construct(Camp $camp)
    {
        $object = array(
                "id" => $camp->getId(),
                "name" => $camp->getName(),
                "title" => $camp->getTitle(),
                "start" => ($camp->getStart() == null) ? null : $camp->getStart()->format(\DateTime::ISO8601),
                "end" => ($camp->getEnd() == null) ? null : $camp->getEnd()->format(\DateTime::ISO8601),
                );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/camps', array('camp' => $camp->getId()));

        $this->getLinks()->add($selfLink);

    }
}
