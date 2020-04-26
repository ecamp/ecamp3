<?php

namespace eCamp\Core\Hydrator;

use ZF\Hal\Link\Link;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Core\Entity\EventPlugin;
use Zend\Hydrator\HydratorInterface;
use eCamp\Core\Plugin\PluginStrategyProvider;
use Psr\Container\NotFoundExceptionInterface;
use eCamp\Core\Plugin\PluginStrategyInterface;
use Psr\Container\ContainerExceptionInterface;

class EventPluginHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
        ];
    }

    /** @var  PluginStrategyProvider */
    private $pluginStrategyProvider;

    public function __construct(PluginStrategyProvider $pluginStrategyProvider) {
        $this->pluginStrategyProvider = $pluginStrategyProvider;
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

            'event_type_plugin' => new EntityLink($eventPlugin->getEventTypePlugin()),

            'event' => Link::factory([
                'rel' => 'event',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event',
                    'params' => [ 'event_id' => $eventPlugin->getEvent()->getId() ]
                ]
            ])
        ];

        /** @var PluginStrategyInterface $strategy */
        $strategy = $this->pluginStrategyProvider->get($plugin);

        if ($strategy != null) {
            $links = $strategy->eventPluginExtract($eventPlugin);
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
