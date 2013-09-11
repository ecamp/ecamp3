<?php
namespace EcampApi\Resource\EventResp;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\EventResp as EventResp;
use EcampApi\Resource\User\UserBriefResource;

class EventRespBriefResource extends HalResource
{
    public function __construct(EventResp $entity)
    {
        $object = array(
                'id'		=> 	$entity->getId(),
                'user'		=>  new UserBriefResource($entity->getUser())

        );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/event_resps', array('event_resp' => $entity->getId()));

        $this->getLinks()->add($selfLink);

    }
}
