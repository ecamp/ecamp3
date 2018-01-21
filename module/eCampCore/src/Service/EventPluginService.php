<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Entity\Plugin;
use eCamp\Core\Hydrator\EventPluginHydrator;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Plugin\PluginStrategyInterface;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\BaseService;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ZF\ApiProblem\ApiProblem;

class EventPluginService extends BaseService
{
    /** @var ContainerInterface */
    private $container;

    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , EventPluginHydrator $eventPluginHydrator
    , ContainerInterface $container
    ) {
        parent::__construct
        ( $acl
        , $entityManager
        , $eventPluginHydrator
        , EventPlugin::class
        );

        $this->container = $container;
    }

    /**
     * @param Plugin $plugin
     * @return PluginStrategyInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getStrategy(Plugin $plugin) {
        $strategy = null;
        $strategyClass = $plugin->getStrategyClass();

        if (is_string($strategyClass)) {
            if ($this->container->has($strategyClass)) {
                $strategy = $this->container->get($strategyClass);
            } else {
                $strategy = new $strategyClass();
            }
        }

        return $strategy;
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
        $strategy = $this->getStrategy($eventTypePlugin->getPlugin());
        if ($strategy != null) {
            $strategy->postCreated($eventPlugin);
        }

        return $eventPlugin;
    }


}
