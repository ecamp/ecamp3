<?php

namespace App\Tests\InputFilter;

use App\InputFilter\CleanTextFilter;
use App\InputFilter\InputFilter;
use App\InputFilter\UnexpectedValueException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
class CleanTextFilterTest extends KernelTestCase {
    private ?InputFilter $inputFilter = null;

    public function setUp(): void {
        parent::setUp();

        $this->inputFilter = new CleanTextFilter();
    }

    /**
     * @dataProvider getExamples
     */
    public function testInputFiltering($input, $output) {
        // given
        $data = ['key' => $input];
        $outputData = ['key' => $output];

        // when
        $result = $this->inputFilter->applyTo($data, 'key');

        // then
        $this->assertEquals($outputData, $result);
    }

    public function getExamples() {
        return [
            ['', ''],
            ['abc', 'abc'],
            ['<tag>', '<tag>'],
            ['😀', '😀'],
            ['keeps \backslash\ and "double quotes"', 'keeps \backslash\ and "double quotes"'],
            ["keeps 'single quotes'", "keeps 'single quotes'"],
            ["removes newlines\n, tabs\t, vertical tabs\v and form-feed\f", 'removes newlines, tabs, vertical tabs and form-feed'],
            ["removes unicode\u{000A} control\u{0007} caracters", 'removes unicode control caracters'],
            ["removes ASCII\x0A control\x07 caracters", 'removes ASCII control caracters'],
            ["removes ANSI escape \e[32m sequences", 'removes ANSI escape [32m sequences'],
            ["removes bidirectional\u{202E} text control", 'removes bidirectional text control'],
            ['removes non-escaped bidirectional‮ text control', 'removes non-escaped bidirectional text control'],
        ];
    }

    public function testDoesNothingWhenKeyIsMissing() {
        // given
        $data = ['otherkey' => 'something'];

        // when
        $result = $this->inputFilter->applyTo($data, 'key');

        // then
        $this->assertEquals($data, $result);
    }

    public function testDoesNothingWhenValueIsNull() {
        // given
        $data = ['key' => null];

        // when
        $result = $this->inputFilter->applyTo($data, 'key');

        // then
        $this->assertEquals($data, $result);
    }

    public function testThrowsWhenValueIsNotStringable() {
        // given
        $data = ['key' => new \stdClass()];

        // then
        $this->expectException(UnexpectedValueException::class);

        // when
        $result = $this->inputFilter->applyTo($data, 'key');
    }
}
