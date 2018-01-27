<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Hydrator\EventPluginHydrator;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Plugin\PluginStrategyInterface;
use eCamp\Core\Plugin\PluginStrategyProvider;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\BaseService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ZF\ApiProblem\ApiProblem;

class EventPluginService extends BaseService
{
    /** @var  PluginStrategyProvider */
    private $pluginStrategyProvider;

    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , EventPluginHydrator $eventPluginHydrator
    , PluginStrategyProvider $pluginStrategyProvider
    ) {
        parent::__construct
        ( $acl
        , $entityManager
        , $eventPluginHydrator
        , EventPlugin::class
        );

        $this->pluginStrategyProvider = $pluginStrategyProvider;
    }


    protected function findCollectionQueryBuilder($className, $params = []) {
        $q = parent::findCollectionQueryBuilder($className, $params);

        $eventId = $params['event_id'];
        if ($eventId) {
            $q->andWhere('row.event = :eventId');
            $q->setParameter('eventId', $eventId);
        }

        return $q;
    }


    /**
     * @param mixed $data
     * @return EventPlugin|ApiProblem
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        /** @var EventPlugin $eventPlugin */
        $eventPlugin = parent::create($data);

        /** @var Event $event */
        $event = $this->findEntity(Event::class, $data->event_id);
        /** @var EventTypePlugin $eventTypePlugin */
        $eventTypePlugin = $this->findEntity(EventTypePlugin::class, $data->event_type_plugin_id);

        $this->assertAllowed($event, Acl::REST_PRIVILEGE_UPDATE);

        $eventPlugin->setEvent($event);
        $eventPlugin->setEventTypePlugin($eventTypePlugin);

        /** @var PluginStrategyInterface $strategy */
        $strategy = $this->pluginStrategyProvider->get($eventTypePlugin->getPlugin());
        if ($strategy != null) {
            $strategy->eventPluginCreated($eventPlugin);
        }

        return $eventPlugin;
    }


}
