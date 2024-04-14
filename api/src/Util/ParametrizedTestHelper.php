<?php

namespace App\Util;

class ParametrizedTestHelper {
    public static function asParameterTestSets(array $array): array {
        return array_reduce($array, function (?array $left, mixed $right) {
            $newArray = $left ?? [];
            $newArray[$right.''] = [$right];

            return $newArray;
        });
    }
}
