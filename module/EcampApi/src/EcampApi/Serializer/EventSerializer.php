<?php

namespace EcampApi\Serializer;

use EcampCore\Entity\Camp;
use EcampCore\Entity\Event;

class EventSerializer extends BaseSerializer
{
    public function serialize($event)
    {
        $campSerializer = new CampSerializer($this->format, $this->router);
        $eventInstanceSerializer = new EventInstanceSerializer($this->format, $this->router);

        return array(
            'id' 				=> 	$event->getId(),
            'href'				=>	$this->getEventHref($event),
            'title'				=> 	$event->getTitle(),
            'camp'				=> 	$campSerializer->getReference($event->getCamp()),
            'eventInstances'	=> 	$eventInstanceSerializer->getCollectionReference($event),
        );
    }

    public function getReference(Event $event = null)
    {
        if ($event == null) {
            return null;
        } else {
            return array(
                'id'	=>	$event->getId(),
                'href'	=>	$this->getEventHref($event)
            );
        }
    }

    public function getCollectionReference($collectionOwner)
    {
        if ($collectionOwner instanceof Camp) {
            return array('href' => $this->getCamp_EventCollectionHref($collectionOwner));
        }

        return null;
    }

    private function getEventHref(Event $event)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'events',
                    'action' => 'get',
                    'id' => $event->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/rest'
                )
        );
    }

    private function getCamp_EventCollectionHref(Camp $camp)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'events',
                    'action' => 'getList',
                    'camp' => $camp->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/camp/rest'
                )
            );
    }

}
