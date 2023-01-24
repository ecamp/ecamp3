<?php

namespace App\Tests\Validator;

use App\Validator\AssertEitherIsNull;
use App\Validator\AssertEitherIsNullValidator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertEitherIsNullValidatorTest extends ConstraintValidatorTestCase {
    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testExpectsRealPropertyName() {
        $this->setObject(new \stdClass());
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate(null, new AssertEitherIsNull(null, 'something'));
    }

    public function testSelfNullValid() {
        // given
        $object = new TestClass(null, 'test');
        $this->setObject($object);

        // when
        $this->validator->validate($object->a, new AssertEitherIsNull(null, 'b'));

        // then
        $this->assertNoViolation();
    }

    public function testOtherNullValid() {
        // given
        $object = new TestClass('test', null);
        $this->setObject($object);

        // when
        $this->validator->validate($object->a, new AssertEitherIsNull(null, 'b'));

        // then
        $this->assertNoViolation();
    }

    public function testBothNullInvalid() {
        // given
        $object = new TestClass(null, null);
        $this->setObject($object);

        // when
        $this->validator->validate($object->a, new AssertEitherIsNull(null, 'b'));

        // then
        $this->buildViolation('Either this value or {{ other }} should not be null.')
            ->setParameter('{{ other }}', 'b')
            ->assertRaised()
        ;
    }

    public function testNeitherNullInvalid() {
        // given
        $object = new TestClass('test', 'value');
        $this->setObject($object);

        // when
        $this->validator->validate($object->a, new AssertEitherIsNull(null, 'b'));

        // then
        $this->buildViolation('Either this value or {{ other }} should be null.')
            ->setParameter('{{ other }}', 'b')
            ->assertRaised()
        ;
    }

    protected function createValidator(): ConstraintValidatorInterface {
        return new AssertEitherIsNullValidator();
    }
}

class TestClass {
    #[AssertEitherIsNull(other: 'b')]
    public $a;

    public $b;

    public function __construct($a, $b) {
        $this->a = $a;
        $this->b = $b;
    }
}
