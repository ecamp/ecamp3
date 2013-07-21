<?php

namespace EcampApi\Serializer;

use EcampCore\Entity\User;
use EcampCore\Entity\Event;
use EcampCore\Entity\EventResp;

class EventRespSerializer extends BaseSerializer
{
    public function serialize($eventResp)
    {
        /* @var $eventResp \EcampCore\Entity\EventResp */

        $userSerializer = new UserSerializer($this->format, $this->router);
        $collaborationSerializer = new CollaborationSerializer($this->format, $this->router);
        $eventSerializer = new EventSerializer($this->format, $this->router);

        return array(
            'id' 				=> 	$eventResp->getId(),
            'href'				=>	$this->getEventRespHref($eventResp),
            'collaboration'		=>  $collaborationSerializer->getReference($eventResp->getCampCollaboration()),
            'user'				=>  $userSerializer->getReference($eventResp->getUser()),
            'event'				=>  $eventSerializer->getReference($eventResp->getEvent())
        );
    }

    public function getReference(Event $event = null)
    {
        if ($event == null) {
            return null;
        } else {
            return array(
                'id'	=>	$event->getId(),
                'href'	=>	$this->getEventRespHref($event)
            );
        }
    }

    public function getCollectionReference($collectionOwner)
    {
        if ($collectionOwner instanceof Event) {
            return array('href' => $this->getEvent_EventRespCollectionHref($collectionOwner));
        }

        return null;
    }

    private function getEventRespHref(EventResp $eventResp)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'eventResps',
                    'action' => 'get',
                    'id' => $eventResp->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/rest'
                )
        );
    }

    private function getEvent_EventRespCollectionHref(Event $event)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'eventResps',
                    'action' => 'getList',
                    'event' => $event->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/event/rest'
                )
            );
    }

}
