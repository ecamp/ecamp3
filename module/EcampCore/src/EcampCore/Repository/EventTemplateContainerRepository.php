<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\Event;
use EcampCore\Entity\EventTemplateContainer;
use EcampCore\Entity\Medium;
use EcampCore\Entity\Plugin;

/**
 * Class EventTemplateContainerRepository
 * @package EcampCore\Repository
 *
 * @method EventTemplateContainer find($id)
 */
class EventTemplateContainerRepository
    extends EntityRepository
{

    /**
     * @param  Event                  $event
     * @param  Plugin                 $plugin
     * @param  Medium                 $medium
     * @return EventTemplateContainer
     */
    public function findTemplateContainer
    (	Event $event
    , 	Plugin $plugin
    , 	Medium $medium
    ){
        $eventType = $event->getEventCategory()->getEventType();

        $q = $this->createQueryBuilder('etc');
        $q->join('etc.eventTypePlugin', 'etp');
        $q->join('etc.eventTemplate', 'etpl');
        $q->where('etpl.eventType = etp.eventType');
        $q->andWhere('etpl.eventType = :eventType');
        $q->andWhere('etp.plugin = :plugin');
        $q->andWhere('etpl.medium = :medium');
        $q->setMaxResults(1);

        $q->setParameter('eventType', $eventType);
        $q->setParameter('plugin', $plugin);
        $q->setParameter('medium', $medium);

        return $q->getQuery()->getSingleResult();
    }

}
