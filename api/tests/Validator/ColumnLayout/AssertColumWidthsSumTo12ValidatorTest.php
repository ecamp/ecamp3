<?php

namespace App\Tests\Validator\ColumnLayout;

use App\Validator\ColumnLayout\AssertColumWidthsSumTo12;
use App\Validator\ColumnLayout\AssertColumWidthsSumTo12Validator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertColumWidthsSumTo12ValidatorTest extends ConstraintValidatorTestCase {
    private const message = 'Expected column widths to sum to 12, but got a sum of {{ sum }}';

    private MockObject|RequestStack $requestStack;

    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testNullThrowsException() {
        $this->expectException(\TypeError::class);
        $this->validator->validate(null, new AssertColumWidthsSumTo12());
    }

    public function testValid() {
        // given
        $data = ['columns' => [
            ['width' => 6],
            ['width' => 5],
            ['width' => 1],
        ]];

        // when
        $this->validator->validate($data, new AssertColumWidthsSumTo12());

        // then
        $this->assertNoViolation();
    }

    public function testInvalid() {
        // given
        $data = ['columns' => [
            ['width' => 6],
            ['width' => 5],
        ]];

        // when
        $this->validator->validate($data, new AssertColumWidthsSumTo12());

        // then
        $this->buildViolation(self::message)->setParameter('{{ sum }}', 11)->assertRaised();
    }

    public function testMissingWidthIsCountedZero() {
        // given
        $data = ['columns' => [
            ['slot' => 'test'],
        ]];

        // when
        $this->validator->validate($data, new AssertColumWidthsSumTo12());

        // then
        $this->buildViolation(self::message)->setParameter('{{ sum }}', 0)->assertRaised();
    }

    protected function createValidator(): ConstraintValidatorInterface {
        $this->requestStack = $this->createMock(RequestStack::class);

        return new AssertColumWidthsSumTo12Validator($this->requestStack);
    }
}
