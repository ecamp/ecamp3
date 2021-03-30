<?php

namespace eCamp\ContentType\ColumnLayoutTest;

use eCamp\ContentType\ColumnLayout\Strategy;
use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class StrategyTest extends AbstractTestCase {
    private ?Strategy $strategy;

    public function setUp(): void {
        parent::setUp();

        $this->strategy = \eCampApp::GetService(Strategy::class);
    }

    public function tearDown(): void {
        parent::tearDown();

        \eCampApp::Reset();
    }

    /**
     * @dataProvider jsonExamples
     *
     * @param $name string a short description of the test case
     * @param null|string $key string the key in the messages array
     * @param $expectedMessages string|null the expected validation message
     * @param $input array|null the jsonConfig to feed into the validator
     */
    public function testJsonConfigValidation(string $name, ?string $key, ?string $expectedMessages, ?array $input): void {
        // given
        $contentNode = new ContentNode();
        $contentNode->setJsonConfig($input);
        $child = new ContentNode();
        $child->setSlot('1');
        $contentNode->getChildren()->add($child);

        // when
        try {
            $this->strategy->validateContentNode($contentNode);

            // then
            if (null !== $expectedMessages) {
                $this->fail('expected test case '.$name.' to throw an EntityValidationException, but it did not');
            } else {
                $this->expectNotToPerformAssertions();
            }
        } catch (EntityValidationException $e) {
            $expected = ['jsonConfig' => [$key => $expectedMessages]];
            $this->assertEquals($expected, $e->getMessages(), 'validation "'.$name.'" failed with wrong message, expected '.var_export($expected, true).', got '.var_export($e->getMessages(), true));
        }
    }

    public function jsonExamples(): array {
        return [
            ['null',                    'invalid',       'Object expected, null received', null],
            ['array',                   'invalid',       'Object expected, [] received', []],
            ['additional property',     'invalid',       'Required property missing: columns, data: {"test":"123"}', ['test' => '123']],
            ['wrong columns data type', 'invalid',       'Array expected, "123" received at #->properties:columns', ['columns' => '123']],
            ['no columns',              'invalidWidths', 'Expected column widths to sum to 12, but got a sum of 0', ['columns' => []], 'invalidWidths'],
            ['wrong column data type',  'invalid',       'Object expected, 1 received at #->properties:columns->items[0]:0', ['columns' => [1]]],
            ['null column',             'invalid',       'Object expected, null received at #->properties:columns->items[0]:0', ['columns' => [null]]],
            ['wrong column data type',  'invalid',       'Object expected, 1 received at #->properties:columns->items[0]:0', ['columns' => [1]]],
            ['additional col property', 'invalid',       'Additional properties not allowed: hello at #->properties:columns->items[0]:0', ['columns' => [['slot' => '1', 'width' => 12, 'hello' => 'world']]]],
            ['missing slot name',       'invalid',       'Required property missing: slot, data: {"width":12} at #->properties:columns->items[0]:0', ['columns' => [['width' => 12]]]],
            ['invalid slot name',       'invalid',       'String expected, null received at #->properties:columns->items[0]:0->properties:slot', ['columns' => [['slot' => null, 'width' => 12]]]],
            ['non-numeric slot name',   'invalid',       '"aaa" does not match to ^[1-9][0-9]*$ at #->properties:columns->items[0]:0->properties:slot', ['columns' => [['slot' => 'aaa', 'width' => 12]]]],
            ['non-number-like slot',    'invalid',       '"01" does not match to ^[1-9][0-9]*$ at #->properties:columns->items[0]:0->properties:slot', ['columns' => [['slot' => '01', 'width' => 12]]]],
            ['missing column width',    'invalid',       'Required property missing: width, data: {"slot":"1"} at #->properties:columns->items[0]:0', ['columns' => [['slot' => '1']]]],
            ['invalid column width',    'invalid',       'Integer expected, "12" received at #->properties:columns->items[0]:0->properties:width', ['columns' => [['slot' => '1', 'width' => '12']]]],
            ['too small column width',  'invalid',       'Value more than 1 expected, 0 received at #->properties:columns->items[0]:0->properties:width', ['columns' => [['slot' => '1', 'width' => 0], ['slot' => '2', 'width' => 12]]]],
            ['too large column width',  'invalid',       'Value less than 12 expected, 13 received at #->properties:columns->items[0]:0->properties:width', ['columns' => [['slot' => '1', 'width' => 13], ['slot' => '2', 'width' => -1]]]],
            ['widths don\'t sum to 12', 'invalidWidths', 'Expected column widths to sum to 12, but got a sum of 13', ['columns' => [['slot' => '1', 'width' => 4], ['slot' => '2', 'width' => 6], ['slot' => '2', 'width' => 3]]]],
            ['default should work',     null,            null, Strategy::$DEFAULT_JSON_CONFIG],
            ['1 column should work',    null,            null, ['columns' => [['slot' => '1', 'width' => 12]]]],
            ['3 columns should work',   null,            null, ['columns' => [['slot' => '1', 'width' => 2], ['slot' => '2', 'width' => 7], ['slot' => '3', 'width' => 3]]]],
            ['non-alphabetic order',    null,            null, ['columns' => [['slot' => '3', 'width' => 3], ['slot' => '2', 'width' => 7], ['slot' => '1', 'width' => 2]]]],
            ['non-standard slot names', null,            null, ['columns' => [['slot' => '1', 'width' => 4], ['slot' => '21', 'width' => 4], ['slot' => '300', 'width' => 4]]]],
            ['orphan children',         'orphanChildContents', 'The following slots still have child contents and should be present in the columns: 1', ['columns' => [['slot' => '2', 'width' => 4], ['slot' => '3', 'width' => 4], ['slot' => '4', 'width' => 4]]]],
        ];
    }
}
