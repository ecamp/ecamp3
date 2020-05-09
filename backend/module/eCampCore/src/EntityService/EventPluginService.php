<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Hydrator\EventPluginHydrator;
use eCamp\Core\Plugin\PluginStrategyProvider;
use eCamp\Core\Plugin\PluginStrategyProviderTrait;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\Authentication\AuthenticationService;
use ZF\ApiProblem\ApiProblem;

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

    /**
     * @param mixed $data
     * @param mixed $persist
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws NoAccessException
     *
     * @return ApiProblem|EventPlugin
     */
    public function create($data, bool $persist = true) {
        /** @var EventPlugin $eventPlugin */
        $eventPlugin = parent::create($data, false);

        /** @var Event $event */
        $event = $this->findEntity(Event::class, $data->eventId);

        /** @var EventTypePlugin $eventTypePlugin */
        $eventTypePlugin = $this->findEntity(EventTypePlugin::class, $data->eventTypePluginId); // POSSIBLE ALTERNATIVE: accept pluginId instead of eventTypePluginId

        // verify EventTypePlugin matches EventType of event
        if ($event->getEventType() !== $eventTypePlugin->getEventType()) {
            throw new \Error("EventType of Event and EventTypePlugin don't match");
        }

        $this->assertAllowed($event, Acl::REST_PRIVILEGE_UPDATE);

        $eventPlugin->setEvent($event);
        $eventPlugin->setPlugin($eventTypePlugin->getPlugin());
        $eventPlugin->setPluginStrategyProvider($this->getPluginStrategyProvider());

        // manual persist necessary because parent::create was called with $persist=false
        if ($persist) {
            $this->getServiceUtils()->emPersist($eventPlugin);
        }

        return $eventPlugin;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.event', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        if (isset($params['eventId'])) {
            $q->andWhere('row.event = :eventId');
            $q->setParameter('eventId', $params['eventId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.event', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        return $q;
    }
}
