<?php

namespace App\Tests\Validator;

use App\Validator\AssertJsonSchema;
use App\Validator\AssertJsonSchemaValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertJsonSchemaValidatorTest extends ConstraintValidatorTestCase {
    private const message = "Provided JSON doesn't match required schema.";

    private const testSchema = [
        'type' => 'object',
        'properties' => [
            'id' => [
                'type' => 'integer',
            ],
            'name' => [
                'type' => 'string',
            ],
        ],
        'required' => ['id'],
        'additionalProperties' => false,
    ];

    private MockObject|RequestStack $requestStack;

    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testNullIsInvalid() {
        // when
        $this->validator->validate(null, new AssertJsonSchema());

        // then
        $this->buildViolation(self::message)->assertRaised();
    }

    public function testEmptyObjectIsValid() {
        // when
        $this->validator->validate(new \stdClass(), new AssertJsonSchema());

        // then
        $this->assertNoViolation();
    }

    public function testCustomSchemaValid() {
        // given
        $value = new \stdClass();
        $value->id = 5;
        $value->name = 'test';

        // when
        $this->validator->validate($value, new AssertJsonSchema(schema: self::testSchema));

        // then
        $this->assertNoViolation();
    }

    public function testCustomSchemaInvalid() {
        // given
        $value = new \stdClass();
        $value->anotherKey = 'anotherValue';

        // when
        $this->validator->validate($value, new AssertJsonSchema(schema: self::testSchema));

        // then
        $this->buildViolation(self::message)->assertRaised();
    }

    protected function createValidator() {
        $this->requestStack = $this->createMock(RequestStack::class);

        return new AssertJsonSchemaValidator($this->requestStack);
    }
}
