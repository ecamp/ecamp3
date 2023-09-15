<?php

namespace App\Tests\Constraints;

use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\logicalNot;

/**
 * @internal
 */
class CompatibleHalResponseTest extends TestCase {
    /**
     * @dataProvider compatibleArrays
     */
    public function testCompatibleArrays(array $array1, array $array2) {
        assertThat($array1, CompatibleHalResponse::isHalCompatibleWith($array2));
    }

    /**
     * @dataProvider compatibleArrays
     */
    public function testCompatibleArraysIsCommutative(array $array1, array $array2) {
        assertThat($array2, CompatibleHalResponse::isHalCompatibleWith($array1));
    }

    public static function compatibleArrays() {
        return [
            'empty' => [[], []],
            'one_key' => [['one' => 1], ['one' => 1]],
            'two_keys' => [
                ['one' => 1, 'two' => 2],
                ['one' => 1, 'two' => 2],
            ],
            'different_values' => [['one' => 1], ['one' => 2]],
            'one_embedded_one_not' => [
                [
                    '_links' => [
                        'one' => 'value',
                    ],
                ],
                [
                    '_links' => [
                        'one' => 'value',
                    ],
                    '_embedded' => [
                        'one' => [
                            '_links' => [
                                'self' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            'recursive' => [
                [
                    '_links' => [
                        'one' => 'value',
                    ],
                    '_embedded' => [
                        'one' => [
                            '_links' => [
                                'self' => 1,
                            ],
                            '_embedded' => [
                                'one' => [
                                    '_links' => [
                                        'self' => 1,
                                    ],
                                ],
                                'two' => [
                                    '_links' => [
                                        'self' => 1,
                                    ],
                                ],
                            ],
                        ],
                        'two' => [
                            '_links' => [
                                'self' => 1,
                            ],
                        ],
                    ],
                ],
                [
                    '_links' => [
                        'one' => 'value',
                    ],
                    '_embedded' => [
                        'one' => [
                            '_links' => [
                                'self' => 1,
                            ],
                            '_embedded' => [
                                'one' => [
                                    '_links' => [
                                        'self' => 1,
                                    ],
                                ],
                                'two' => [
                                    '_links' => [
                                        'self' => 1,
                                    ],
                                ],
                            ],
                        ],
                        'two' => [
                            '_links' => [
                                'self' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider notCompatibleArrays
     */
    public function testNotCompatibleArrays(array $array1, array $array2) {
        assertThat($array1, logicalNot(CompatibleHalResponse::isHalCompatibleWith($array2)));
    }

    /**
     * @dataProvider notCompatibleArrays
     */
    public function testNotCompatibleArraysIsCommutative(array $array1, array $array2) {
        assertThat($array2, logicalNot(CompatibleHalResponse::isHalCompatibleWith($array1)));
    }

    public static function notCompatibleArrays() {
        return [
            'empty_and_not_empty' => [[], [2]],
            'one_key' => [['one' => 1], ['two' => 1]],
            'two_keys' => [
                ['one' => 1, 'three' => 2],
                ['one' => 1, 'two' => 2],
            ],
            'one_embedded_one_not' => [
                [
                    '_links' => [
                        'two' => 'value',
                    ],
                ],
                [
                    '_links' => [
                        'one' => 'value',
                    ],
                    '_embedded' => [
                        'one' => [
                            '_links' => [
                                'self' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            'recursive' => [
                [
                    '_links' => [
                        'one' => 'value',
                    ],
                    '_embedded' => [
                        'one' => [
                            '_links' => [
                                'self' => 1,
                            ],
                            '_embedded' => [
                                'one' => [
                                    '_links' => [
                                        'self' => 1,
                                    ],
                                ],
                                'two' => [
                                    '_links' => [
                                        'self' => 1,
                                    ],
                                ],
                            ],
                        ],
                        'two' => [
                            '_links' => [
                                'self' => 1,
                            ],
                        ],
                    ],
                ],
                [
                    '_links' => [
                        'one' => 'value',
                    ],
                    '_embedded' => [
                        'one' => [
                            '_links' => [
                                'self' => 1,
                            ],
                            '_embedded' => [
                                'one' => [
                                    '_links' => [
                                        'self' => 1,
                                    ],
                                ],
                                'three' => [
                                    '_links' => [
                                        'self' => 1,
                                    ],
                                ],
                            ],
                        ],
                        'two' => [
                            '_links' => [
                                'self' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
