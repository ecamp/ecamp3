<?php

namespace eCamp\AoT;

use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class DiGenerateAotTest extends AbstractTestCase {
    const GEN_DIRECTORY = __DIR__.'/../../../gen_tmp';

    public function testRunsThroughWithoutErrors() {
        // delete folder
        if (file_exists(self::GEN_DIRECTORY)) {
            (new Filesystem())->remove(self::GEN_DIRECTORY);
        }

        // given
        $this->assertDirectoryDoesNotExist(self::GEN_DIRECTORY);

        // recreate empty folder
        (new Filesystem())->mkdir(self::GEN_DIRECTORY);

        // when
        // require self::SCRIPT_PATH;
        $builder = new FactoryBuilder();
        $builder->useTempFolder();
        $builder->build();

        // then
        $this->assertDirectoryExists(self::GEN_DIRECTORY);
        $this->assertDirectoryExists(self::GEN_DIRECTORY.'/Factory/eCamp');
        $this->assertDirectoryExists(self::GEN_DIRECTORY.'/Factory/eCampApi');

        // Recreate empty folder
        (new Filesystem())->remove(self::GEN_DIRECTORY);
    }
}
