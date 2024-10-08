<?php

namespace App\Tests\InputFilter;

use App\InputFilter\LowercaseFilter;
use App\InputFilter\UnexpectedValueException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class LowercaseFilterTest extends TestCase {
    #[DataProvider('getExamples')]
    public function testInputFiltering(string $input, string $output): void {
        // given
        $data = ['key' => $input];
        $outputData = ['key' => $output];
        $trim = new LowercaseFilter();

        // when
        $result = $trim->applyTo($data, 'key');

        // then
        $this->assertEquals($outputData, $result);
    }

    public static function getExamples(): array {
        return [
            ['', ''],
            ['abc', 'abc'],
            ['AbC', 'abc'],
            ['This Is A Test', 'this is a test'],
            ['TeSt123', 'test123'],
            ['Test@Example.com', 'test@example.com'],
            ['JohnDoe@GMail.COM', 'johndoe@gmail.com'],
            ['USER-NAME+TAG@EXAMPLE.NET', 'user-name+tag@example.net'],
            ['info@sub.domain.COM', 'info@sub.domain.com'],
        ];
    }

    public function testDoesNothingWhenKeyIsMissing(): void {
        // given
        $data = ['otherkey' => 'something'];
        $lowercase = new LowercaseFilter();

        // when
        $result = $lowercase->applyTo($data, 'key');

        // then
        $this->assertEquals($data, $result);
    }

    public function testDoesNothingWhenValueIsNull(): void {
        // given
        $data = ['key' => null];
        $trim = new LowercaseFilter();

        // when
        $result = $trim->applyTo($data, 'key');

        // then
        $this->assertEquals($data, $result);
    }

    public function testThrowsWhenValueIsNotStringable(): void {
        // given
        $data = ['key' => new \stdClass()];
        $trim = new LowercaseFilter();

        // then
        $this->expectException(UnexpectedValueException::class);

        // when
        $trim->applyTo($data, 'key');
    }
}
