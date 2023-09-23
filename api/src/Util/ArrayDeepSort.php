<?php

namespace App\Util;

class ArrayDeepSort {
    /**
     * @phpstan-template T
     *
     * @phpstan-param T $input
     *
     * @psalm-param mixed $input
     *
     * @phpstan-return T
     *
     * @psalm-return mixed
     */
    public static function sort(mixed $input): mixed {
        if (!is_array($input)) {
            return $input;
        }
        $sorted_children = array_map([ArrayDeepSort::class, 'sort'], $input);

        if (array_is_list($sorted_children)) {
            uasort($sorted_children, function ($a, $b) {
                $valueStringA = json_encode($a);
                $valueStringB = json_encode($b);

                return strcmp($valueStringA, $valueStringB);
            });

            return array_values($sorted_children);
        }

        ksort($sorted_children);

        return $sorted_children;
    }
}
