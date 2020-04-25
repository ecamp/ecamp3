<?php

namespace eCamp\Core\EntityService;

use eCamp\Lib\Acl\Acl;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Event;
use ZF\ApiProblem\ApiProblem;
use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Lib\Service\ServiceUtils;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Hydrator\EventPluginHydrator;
use eCamp\Core\Plugin\PluginStrategyProvider;
use Psr\Container\NotFoundExceptionInterface;
use eCamp\Core\Plugin\PluginStrategyInterface;
use Psr\Container\ContainerExceptionInterface;
use Zend\Authentication\AuthenticationService;
use eCamp\Core\Plugin\PluginStrategyProviderAware;
use eCamp\Core\Plugin\PluginStrategyProviderTrait;

class EventPluginService extends AbstractEntityService {
    use PluginStrategyProviderTrait;

    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService, PluginStrategyProvider $pluginStrategyProvider) {
        parent::__construct(
            $serviceUtils,
            EventPlugin::class,
            EventPluginHydrator::class,
            $authenticationService
        );

        $this->setPluginStrategyProvider($pluginStrategyProvider);
    }


    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.event', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        if (isset($params['event_id'])) {
            $q->andWhere('row.event = :eventId');
            $q->setParameter('eventId', $params['event_id']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.event', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

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
        $em = $this->getServiceUtils()->entityManager;

        /** @var EventPlugin $eventPlugin */
        $eventPlugin = parent::create($data, false);

        /** @var Event $event */
        $event = $this->findEntity(Event::class, $data->event_id);

        /** @var EventTypePlugin $eventTypePlugin */
        $eventTypePlugin = $this->findEntity(EventTypePlugin::class, $data->event_type_plugin_id);

        // verify EventTypePlugin matches EventType of event
        if ($event->getEventType() !== $eventTypePlugin->getEventType()) {
            throw new \Error("EventType of Event and EventTypePlugin don't match");
        }

        $this->assertAllowed($event, Acl::REST_PRIVILEGE_UPDATE);

        $eventPlugin->setEvent($event);
        $eventPlugin->setEventTypePlugin($eventTypePlugin);
        $eventPlugin->setPluginStrategyProvider($this->getPluginStrategyProvider());

        // manual persist necessary because parent::create was called with $persist=false
        $this->getServiceUtils()->emPersist($eventPlugin);

        return $eventPlugin;
    }
}
