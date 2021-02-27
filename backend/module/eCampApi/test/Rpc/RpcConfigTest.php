<?php

namespace eCampApiTest\Rpc;

use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Laminas\Config\Factory as ConfigFactory;

/**
 * @internal
 */
class RpcConfigTest extends AbstractTestCase {
    public function testProducesSameArray() {
        $before = ConfigFactory::fromFile(__DIR__.'/test.module.config.php');

        $after = ConfigFactory::fromFiles(
            array_merge(
                glob(__DIR__.'/../../config/Rpc/*.config.php')
            )
        );

        self::assertThat(var_export($after, true), self::equalTo(var_export($before, true)));
    }
}
