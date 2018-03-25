<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Medium;
use eCamp\Core\Entity\Plugin;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class PluginTest extends AbstractTestCase
{

    public function testPlugin() {
        $plugin = new Plugin();
        $plugin->setName('TestPlugin');
        $plugin->setActive(true);
        $plugin->setStrategyClass('PluginStrategyClass');

        $this->assertEquals('TestPlugin', $plugin->getName());
        $this->assertTrue($plugin->getActive());
        $this->assertEquals('PluginStrategyClass', $plugin->getStrategyClass());
    }

}
