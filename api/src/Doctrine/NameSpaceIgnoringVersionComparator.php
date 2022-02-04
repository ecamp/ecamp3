<?php

namespace App\Doctrine;

use Doctrine\Migrations\Version\Comparator;
use Doctrine\Migrations\Version\Version;

class NameSpaceIgnoringVersionComparator implements Comparator {
    public function compare(Version $a, Version $b): int {
        return strcmp(self::versionWithoutNamespace($a), self::versionWithoutNamespace($b));
    }

    private static function versionWithoutNamespace(Version $version): string {
        $path = explode('\\', (string) $version);

        return array_pop($path);
    }
}
