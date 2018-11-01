<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Entity\Plugin;
use eCamp\Core\Hydrator\EventPluginHydrator;
use eCamp\Core\Plugin\PluginStrategyInterface;
use eCamp\Core\Plugin\PluginStrategyProviderAware;
use eCamp\Core\Plugin\PluginStrategyProviderTrait;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ZF\ApiProblem\ApiProblem;

class EventPluginService extends AbstractEntityService
    implements PluginStrategyProviderAware {
    use PluginStrategyProviderTrait;


    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            EventPlugin::class,
            EventPluginHydrator::class
        );
    }


    protected function findCollectionQueryBuilder($className, $alias, $params = []) {
        $q = parent::findCollectionQueryBuilder($className, $alias);

        $event = $this->getEntityFromData(Event::class, $params, 'event');
        if ($event) {
            $q->andWhere('row.event = :event');
            $q->setParameter('event', $event);
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
        /** @var Event $event */
        $event = $this->getEntityFromData(Event::class, $data, 'event');
        /** @var EventTypePlugin $eventTypePlugin */
        $eventTypePlugin = $this->getEntityFromData(EventTypePlugin::class, $data, 'event_type_plugin');

        $this->assertAllowed($event, Acl::REST_PRIVILEGE_UPDATE);

        /** @var EventPlugin $eventPlugin */
        $eventPlugin = parent::create($data);
        $eventPlugin->setEvent($event);
        $eventPlugin->setEventTypePlugin($eventTypePlugin);

        /** @var Plugin $plugin */
        $plugin = $eventTypePlugin->getPlugin();
        /** @var PluginStrategyInterface $strategy */
        $strategy = $this->getPluginStrategyProvider()->get($plugin);
        if ($strategy != null) {
            $strategy->eventPluginCreated($eventPlugin);
        }

        return $eventPlugin;
    }
}
