<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Hydrator\EventPluginHydrator;
use eCamp\Core\Plugin\PluginStrategyInterface;
use eCamp\Core\Plugin\PluginStrategyProviderAware;
use eCamp\Core\Plugin\PluginStrategyProviderTrait;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\Authentication\AuthenticationService;
use ZF\ApiProblem\ApiProblem;

class EventPluginService extends AbstractEntityService implements PluginStrategyProviderAware {
    use PluginStrategyProviderTrait;


    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            EventPlugin::class,
            EventPluginHydrator::class,
            $authenticationService
        );
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
        $strategy = $this->getPluginStrategyProvider()->get($eventTypePlugin->getPlugin());
        if ($strategy != null) {
            $strategy->eventPluginCreated($eventPlugin);
        }

        return $eventPlugin;
    }
}
