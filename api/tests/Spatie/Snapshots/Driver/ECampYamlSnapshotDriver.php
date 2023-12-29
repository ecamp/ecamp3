<?php

namespace App\Tests\Spatie\Snapshots\Driver;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Symfony\Component\Yaml\Yaml;

class ECampYamlSnapshotDriver implements Driver {
    public function serialize($data): string {
        if (is_string($data)) {
            return $data;
        }

        return Yaml::dump($data, PHP_INT_MAX);
    }

    public function extension(): string {
        return 'yml';
    }

    public function match($expected, $actual) {
        if (is_array($actual)) {
            $actual = Yaml::dump($actual, PHP_INT_MAX);
        }

        Assert::assertEquals($expected, $actual);
    }
}
