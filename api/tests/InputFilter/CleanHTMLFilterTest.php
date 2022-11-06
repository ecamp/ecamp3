<?php

namespace App\Tests\InputFilter;

use App\InputFilter\CleanHTMLFilter;
use App\InputFilter\InputFilter;
use App\InputFilter\UnexpectedValueException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
class CleanHTMLFilterTest extends KernelTestCase {
    private ?InputFilter $inputFilter = null;

    public function setUp(): void {
        parent::setUp();

        $purifierMock = $this->createMock(\HTMLPurifier::class);
        $purifierMock->method('purify')->willReturnArgument(0);

        $this->inputFilter = new CleanHTMLFilter($purifierMock);
    }

    /**
     * @dataProvider getExamples
     */
    public function testInputFiltering($input, $output) {
        // given
        $data = ['key' => $input];
        $outputData = ['key' => $output];

        // For this test, use a real purifier, so we can check that the
        // purifier is configured correctly
        static::bootKernel();
        $this->inputFilter = static::getContainer()->get(CleanHTMLFilter::class);

        // when
        $result = $this->inputFilter->applyTo($data, 'key');

        // then
        $this->assertEquals($outputData, $result);
    }

    public function getExamples() {
        return [
            ['', ''],
            ['abc', 'abc'],
            ['<b>abc</b>', '<b>abc</b>'],
            ['<3', '&lt;3'],
            ['<script>alert(1)</script>', ''],
            ['<span onload="alert(1)">123</span>', '<span>123</span>'],
            ['abc<li>def', 'abcdef'],
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
