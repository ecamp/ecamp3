<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\Plugin;

class PluginTest extends \PHPUnit_Framework_TestCase
{

    public function testPlugin()
    {
        $plugin = new Plugin('plugin name', 'plugin strategy class');
        $plugin->setActive(true);

        $this->assertEquals('plugin name', $plugin->getName());
        $this->assertEquals('plugin strategy class', $plugin->getStrategyClass());

        $this->assertTrue($plugin->getActive());

    }

}
