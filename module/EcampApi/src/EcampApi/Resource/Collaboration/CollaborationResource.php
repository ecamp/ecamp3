<?php
namespace EcampApi\Resource\Collaboration;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\CampCollaboration as Collaboration;
use EcampApi\Resource\User\UserBriefResource;
use EcampApi\Resource\Camp\CampBriefResource;

class CollaborationResource extends HalResource
{
    public function __construct(Collaboration $entity)
    {
        $object = array(
                'id'		=> 	$entity->getId(),
                'user'		=>	new UserBriefResource($entity->getUser()),
                'camp'		=>	new CampBriefResource($entity->getCamp()),
                'role'		=>	$entity->getRole(),
                'status'	=>  $entity->getStatus()
                );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/collaborations', array('collaboration' => $entity->getId()));

        $eventRespLink = new Link('event_resps');
        $eventRespLink->setRoute('api/collaborations/event_resps', array('collaboration' => $entity->getId()));

        $this->getLinks()->add($selfLink)
                         ->add($eventRespLink);

    }
}
