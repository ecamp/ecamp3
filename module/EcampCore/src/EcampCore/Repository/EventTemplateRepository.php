<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\Event;
use EcampCore\Entity\Medium;
use EcampCore\Entity\EventTemplate;

/**
 * Class EventTemplateRepository
 * @package EcampCore\Repository
 *
 * @method EventTemplate find($id)
 */
class EventTemplateRepository
    extends EntityRepository
{
    /**
     * @param  Event         $event
     * @param  Medium        $medium
     * @return EventTemplate
     */
    public function findTemplate(Event $event, Medium $medium)
    {
        $eventType = $event->getEventCategory()->getEventType();

        return $this->findOneBy(array(
            'eventType' => $eventType,
            'medium' => $medium
        ));
    }

}
