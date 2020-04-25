<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Entity\Plugin;
use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Zend\Json\Json;

/**
 * @internal
 * @coversNothing
 */
class EventTypePluginTest extends AbstractTestCase {
    public function testEventTypePlugin() {
        $eventType = new EventType();
        $plugin = new Plugin();
        $config = Json::encode(['test' => 4]);

        $eventTypePlugin = new EventTypePlugin();
        $eventTypePlugin->setEventType($eventType);
        $eventTypePlugin->setPlugin($plugin);
        $eventTypePlugin->setMinNumberPluginInstances(1);
        $eventTypePlugin->setMaxNumberPluginInstances(3);
        $eventTypePlugin->setJsonConfig($config);

        $this->assertEquals($eventType, $eventTypePlugin->getEventType());
        $this->assertEquals($plugin, $eventTypePlugin->getPlugin());
        $this->assertEquals(1, $eventTypePlugin->getMinNumberPluginInstances());
        $this->assertEquals(3, $eventTypePlugin->getMaxNumberPluginInstances());
        $this->assertEquals($config, $eventTypePlugin->getJsonConfig());

        $this->assertEquals(4, $eventTypePlugin->getConfig('test'));
    }
}
