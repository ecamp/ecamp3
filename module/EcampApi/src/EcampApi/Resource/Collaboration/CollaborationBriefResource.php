<?php
namespace EcampApi\Resource\Collaboration;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\CampCollaboration as Collaboration;

class CollaborationBriefResource extends HalResource
{
    public function __construct(Collaboration $entity)
    {
        $object = array(
                'id'		=> 	$entity->getId(),
                'user'		=>	$entity->getUser()->getId(),
                'camp'		=>	$entity->getCamp()->getId(),
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
