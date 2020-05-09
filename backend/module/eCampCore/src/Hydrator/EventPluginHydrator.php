<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Plugin\PluginStrategyInterface;
use eCamp\Core\Plugin\PluginStrategyProvider;
use eCamp\Lib\Entity\EntityLink;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

class EventPluginHydrator implements HydratorInterface {
    /** @var PluginStrategyProvider */
    private $pluginStrategyProvider;

    public function __construct(PluginStrategyProvider $pluginStrategyProvider) {
        $this->pluginStrategyProvider = $pluginStrategyProvider;
    }

    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return array
     */
    public function extract($object) {
        /** @var EventPlugin $eventPlugin */
        $eventPlugin = $object;
        $plugin = $eventPlugin->getPlugin();

        $data = [
            'id' => $eventPlugin->getId(),
            'instanceName' => $eventPlugin->getInstanceName(),
            'pluginName' => $plugin->getName(),

            'plugin' => new EntityLink($eventPlugin->getPlugin()),

            'event' => Link::factory([
                'rel' => 'event',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event',
                    'params' => ['eventId' => $eventPlugin->getEvent()->getId()],
                ],
            ]),
        ];

        /** @var PluginStrategyInterface $strategy */
        $strategy = $this->pluginStrategyProvider->get($plugin);

        if (null != $strategy) {
            $strategyData = $strategy->eventPluginExtract($eventPlugin);
            $data = array_merge($data, $strategyData);
        }

        return $data;
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventPlugin $eventPlugin */
        $eventPlugin = $object;

        if (isset($data['instanceName'])) {
            $eventPlugin->setInstanceName($data['instanceName']);
        }

        return $eventPlugin;
    }
}
