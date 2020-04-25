<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Plugin;
use eCamp\Core\Hydrator\PluginHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 * @coversNothing
 */
class PluginHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $plugin = new Plugin();
        $plugin->setName('name');
        $plugin->setActive(true);
        $plugin->setStrategyClass('TestClass');

        $hydrator = new PluginHydrator();
        $data = $hydrator->extract($plugin);

        $this->assertEquals('name', $data['name']);
        $this->assertTrue($data['active']);
    }

    public function testHydrate() {
        $plugin = new Plugin();
        $data = [
            'name' => 'name',
            'active' => true,
        ];

        $hydrator = new PluginHydrator();
        $hydrator->hydrate($data, $plugin);

        $this->assertEquals('name', $plugin->getName());
        $this->assertTrue($plugin->getActive());
    }
}
