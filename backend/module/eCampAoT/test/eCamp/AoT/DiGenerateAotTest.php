<?php

namespace eCamp\AoT;

use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class DiGenerateAotTest extends AbstractTestCase {
    public const GEN_DIRECTORY = __DIR__.'/../../../gen_tmp';

    public function testRunsThroughWithoutErrors(): void {
        // delete temporary output folder if it exists
        // should not be the case
        if (file_exists(self::GEN_DIRECTORY)) {
            (new Filesystem())->remove(self::GEN_DIRECTORY);
        }

        // check temporary folder does not exist
        $this->assertDirectoryDoesNotExist(self::GEN_DIRECTORY);

        // recreate temporary output folder
        (new Filesystem())->mkdir(self::GEN_DIRECTORY);

        // create factories
        $builder = new FactoryBuilder();
        $builder->setOutputDirectory(self::GEN_DIRECTORY);
        $builder->build();

        // check factories are created
        $this->assertDirectoryExists(self::GEN_DIRECTORY);
        $this->assertDirectoryExists(self::GEN_DIRECTORY.'/Factory/eCamp');
        $this->assertDirectoryExists(self::GEN_DIRECTORY.'/Factory/eCampApi');

        // delete temporary output folder
        (new Filesystem())->remove(self::GEN_DIRECTORY);

        // check temporary folder does not exist
        $this->assertDirectoryDoesNotExist(self::GEN_DIRECTORY);
    }
}
