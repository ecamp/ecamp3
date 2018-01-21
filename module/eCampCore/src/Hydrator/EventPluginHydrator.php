<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Plugin\PluginStrategyInterface;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

class EventPluginHydrator implements HydratorInterface
{
    /** @var  ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }


    /**
     * @param object $object
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function extract($object) {
        /** @var EventPlugin $eventPlugin */
        $eventPlugin = $object;
        $plugin = $eventPlugin->getPlugin();

        $data = [
            'id' => $eventPlugin->getId(),
            'instance_name' => $eventPlugin->getInstanceName(),
            'plugin_name' => $plugin->getName(),

            'event_type_plugin' => $eventPlugin->getEventTypePlugin(),
            'plugin' => $plugin,
            'event' => Link::factory([
                'rel' => 'event',
                'route' => [
                    'name' => 'ecamp.api.event',
                    'params' => [ 'event_id' => $eventPlugin->getEvent()->getId() ]
                ]
            ])
        ];


        /** @var PluginStrategyInterface $strategy */
        $strategy = null;
        $strategyClass = $plugin->getStrategyClass();

        if (is_string($strategyClass)) {
            if ($this->container->has($strategyClass)) {
                $strategy = $this->container->get($strategyClass);
            } elseif (class_exists($strategyClass)) {
                $strategy = new $strategyClass();
            }
        }
        if ($strategy != null) {
            $links = $strategy->getHalLinks($eventPlugin);
            $data = array_merge($data, $links);
        }

        return $data;
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventPlugin $eventPlugin */
        $eventPlugin = $object;

        $eventPlugin->setInstanceName($data['instance_name']);

        return $eventPlugin;
    }
}
