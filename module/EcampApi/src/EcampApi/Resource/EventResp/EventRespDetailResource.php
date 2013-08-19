<?php
namespace EcampApi\Resource\EventResp;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\EventResp as EventResp;
use EcampApi\Resource\Collaboration\CollaborationResource;
use EcampApi\Resource\User\UserBriefResource;
use EcampApi\Resource\Event\EventBriefResource;

class EventRespDetailResource extends HalResource
{
    public function __construct(EventResp $entity)
    {
        $object = array(
                'id'		=> 	$entity->getId(),
                'collaboration'		=>  new CollaborationResource($entity->getCampCollaboration()),
                'user'				=>  new UserBriefResource($entity->getUser()),
                'event'				=>  new EventBriefResource($entity->getEvent())

        );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/event_resps', array('event_resp' => $entity->getId()));

        $this->getLinks()->add($selfLink);

    }
}
