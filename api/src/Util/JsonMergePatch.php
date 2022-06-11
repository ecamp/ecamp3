<?php

namespace App\Util;

class JsonMergePatch {
    /**
     * implements JSON merge patch (https://datatracker.ietf.org/doc/html/rfc7386)
     * partially copied by discussion under https://www.php.net/manual/de/function.array-merge-recursive.php.
     */
    public static function mergePatch(array &$target, array &$patch) {
        $merged = $target;

        foreach ($patch as $key => &$value) {
            // null values can be used to remove keys/elements from the structure
            if (is_null($value)) {
                unset($merged[$key]);

                continue;
            }

            // associative arrays --> merge recursively
            if (is_array($value) && self::isAssoc($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::mergePatch($merged[$key], $value);

                continue;
            }

            // primitve values or sequential/numeric arrays (true JSON arrays)
            $merged[$key] = $value; // TODO: Consider/discuss to apply CleanHTMLFilter cirectly here for all string values
        }

        return $merged;
    }

    /**
     * checks if array is an associative array
     * true if associative: ['id1' => 'value1', 'id2' => 'value2']
     * false if sequential: ['value1', 'value2'].
     */
    private static function isAssoc(array $arr) {
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
