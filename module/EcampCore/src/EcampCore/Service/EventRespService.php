<?php

namespace EcampCore\Service;

use EcampLib\Service\ServiceBase;
use EcampCore\Repository\EventRespRepository;
use EcampCore\Entity\Event;
use EcampCore\Entity\EventResp;

class EventRespService
    extends ServiceBase
{
    /**
     * @var \EcampCore\Repository\EventRespRepository
     */
    private $eventRespRepository;

    public function __construct(
        EventRespRepository $eventRespRepository
    ){
        $this->eventRespRepository = $eventRespRepository;
    }

    public function setResponsableUsers(Event $event, array $users)
    {
        $eventResps = $this->eventRespRepository->findBy(array('event' => $event));

        foreach ($eventResps as $eventResp) {
            /* @var $eventResp \EcampCore\Entity\EventResp */
            if (!in_array($eventResp->getUser(), $users)) {
                $this->remove($eventResp);
            }
        }

        foreach ($users as $user) {
            if (! $event->isUserResp($user)) {
                $campCollaboration = $event->getCamp()->campCollaboration()->getCollaboration($user);
                $eventResp = new EventResp($event, $campCollaboration);
                $this->persist($eventResp);
            }
        }
    }

}
