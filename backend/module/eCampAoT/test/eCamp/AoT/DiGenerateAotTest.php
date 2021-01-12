<?php

namespace eCamp\AoT;

use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class DiGenerateAotTest extends AbstractTestCase {
    const SCRIPT_PATH = __DIR__.'/../../../../../bin/di-generate-aot.php';
    const GEN_DIRECTORY = __DIR__.'/../../../gen';

    protected function setUp(): void {
        parent::setUp();
        $this->removeDirectory(self::GEN_DIRECTORY);
    }

    public function testRunsThroughWithoutErrors() {
        // given
        $this->assertDirectoryDoesNotExist(self::GEN_DIRECTORY);

        // when
        require self::SCRIPT_PATH;

        // then
        $this->assertDirectoryExists(self::GEN_DIRECTORY);
        $this->assertDirectoryExists(self::GEN_DIRECTORY.'/Factory/eCamp');
        $this->assertDirectoryExists(self::GEN_DIRECTORY.'/Factory/eCampApi');
    }

    protected function removeDirectory($dir) {
        if (file_exists($dir)) {
            (new Filesystem())->remove($dir);
        }
    }
}
