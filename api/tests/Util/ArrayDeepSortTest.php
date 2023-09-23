<?php

namespace App\Tests\Util;

use App\Util\ArrayDeepSort;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

/**
 * @internal
 */
class ArrayDeepSortTest extends TestCase {
    use MatchesSnapshots;

    /**
     * @dataProvider getJsonFiles
     */
    public function testSortsJson1AlwaysTheSame(string $jsonFileName) {
        $json1 = file_get_contents(__DIR__."/Resource/{$jsonFileName}");
        $array = json_decode($json1, true);

        $sortedArray = ArrayDeepSort::sort($array);

        $this->assertMatchesJsonSnapshot($sortedArray);
    }

    public static function getJsonFiles() {
        return [
            'json_to_sort_1.json' => ['json_to_sort_1.json'],
            'json_to_sort_2.json' => ['json_to_sort_2.json'],
            'json_to_sort_3.json' => ['json_to_sort_3.json'],
        ];
    }

    public function testSortsAssocArrayByKey() {
        $sorted = ArrayDeepSort::sort(['z' => 0, 'a' => 1]);

        assertThat($sorted, equalTo(['a' => 1, 'z' => 0]));
    }

    public function testSortsListByValueWithStringCompare() {
        $sorted = ArrayDeepSort::sort([1, 'test', 11, 2]);

        assertThat($sorted, equalTo(['test', 1, 11, 2]));
    }

    public function testSortsNestedArrays() {
        $sorted = ArrayDeepSort::sort([
            'totalItems' => 'escaped_value',
            '_links' => [
                'items' => [
                    [
                        'href' => [
                            'z',
                        ],
                    ],
                    [
                        'href' => [
                            'a',
                        ],
                    ],
                ],
            ],
        ]);

        assertThat(json_encode($sorted), equalTo(json_encode([
            '_links' => [
                'items' => [
                    [
                        'href' => [
                            'a',
                        ],
                    ],
                    [
                        'href' => [
                            'z',
                        ],
                    ],
                ],
            ],
            'totalItems' => 'escaped_value',
        ])));
    }
}
