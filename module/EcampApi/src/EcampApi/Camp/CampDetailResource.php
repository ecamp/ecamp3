<?php
namespace EcampApi\Camp;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\Camp;
use EcampApi\User\UserBriefResource;

class CampDetailResource extends HalResource
{
    public function __construct(Camp $camp)
    {
        $object = array(
                "id" => $camp->getId(),
                "name" => $camp->getName(),
                "title" => $camp->getTitle(),
                "start" => ($camp->getStart() == null) ? null : $camp->getStart()->format(\DateTime::ISO8601),
                "end" => ($camp->getEnd() == null) ? null : $camp->getEnd()->format(\DateTime::ISO8601),
                "camp_type" => $camp->getCampType()->getName(),
                "motto" => $camp->getMotto(),
                "visibility" => $camp->getVisibility(),
                "creator" => new UserBriefResource($camp->getCreator())
                );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/camps', array('camp' => $camp->getId()));

        $collabLink = new Link('collaborations');
        $collabLink->setRoute('api/camps/collaborations', array('camp' => $camp->getId()));

        $eventLink = new Link('events');
        $eventLink->setRoute('api/camps/events', array('camp' => $camp->getId()));

        $periodLink = new Link('periods');
        $periodLink->setRoute('api/camps/periods', array('camp' => $camp->getId()));

        $eventInstanceLink = new Link('event_instances');
        $eventInstanceLink->setRoute('api/camps/event_instances', array('camp' => $camp->getId()));

        $eventCategoryLink = new Link('event_categories');
        $eventCategoryLink->setRoute('api/camps/event_categories', array('camp' => $camp->getId()));

        $this->getLinks()->add($selfLink)
                        ->add($collabLink)
                        ->add($eventLink)
                        ->add($periodLink)
                        ->add($eventInstanceLink)
                        ->add($eventCategoryLink);

    }
}
