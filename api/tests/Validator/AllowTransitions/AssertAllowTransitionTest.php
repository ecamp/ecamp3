<?php

namespace App\Tests\Validator\AllowTransitions;

use App\Validator\AllowTransition\AssertAllowTransitions;
use App\Validator\AllowTransition\AssertAllowTransitionsValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertAllowTransitionTest extends ConstraintValidatorTestCase {
    private const TRANSITIONS = [
        ['from' => '1', 'to' => ['2', '3']],
        ['from' => '2', 'to' => ['3']],
        ['from' => '3', 'to' => ['1']],
    ];
    private RequestStack|MockObject $requestStack;
    private TestClass $before;

    protected function setUp(): void {
        $this->before = new TestClass('2');

        $this->requestStack = $this->createMock(RequestStack::class);
        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['previous_data' => $this->before]);
        $this->requestStack->method('getCurrentRequest')->willReturn($request);

        parent::setUp();
    }

    public function testNotValidWhenPreviousValueIsNotInFrom() {
        $this->before->setA('4');
        $testClass = new TestClass('1');
        $this->setProperty($testClass, 'a');

        $this->validator->validate($testClass->a, new AssertAllowTransitions(self::TRANSITIONS));

        $allFrom = (new ArrayCollection(self::TRANSITIONS))->map(fn ($elem) => $elem['from'])->toArray();
        $this->buildViolation(AssertAllowTransitionsValidator::FROM_VIOLATION_MESSAGE)
            ->setParameter('{{ from }}', join(',', $allFrom))
            ->setParameter('{{ previousValue }}', $this->before->a)
            ->assertRaised()
        ;
    }

    public function testValidWhenValueDoesNotChange() {
        $this->before->setA('1');
        $testClass = new TestClass('1');
        $this->setProperty($testClass, 'a');

        $this->validator->validate($testClass->a, new AssertAllowTransitions(self::TRANSITIONS));

        $this->assertNoViolation();
    }

    public function testNotValidWhenValueIsNotTo() {
        $this->before->setA('1');
        $testClass = new TestClass('4');
        $this->setProperty($testClass, 'a');

        $this->validator->validate($testClass->a, new AssertAllowTransitions(self::TRANSITIONS));

        $this->buildViolation(AssertAllowTransitionsValidator::TO_VIOLATION_MESSAGE)
            ->setParameter('{{ to }}', join(', ', self::TRANSITIONS[0]['to']))
            ->setParameter('{{ value }}', $testClass->a)
            ->assertRaised()
        ;
    }

    public function testValidWhenValueInTo() {
        $this->before->setA('1');
        $testClass = new TestClass('2');
        $this->setProperty($testClass, 'a');

        $this->validator->validate($testClass->a, new AssertAllowTransitions(self::TRANSITIONS));

        $this->assertNoViolation();
    }

    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testThrowsInvalidArgumentExceptionForInvalidTransitions() {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate(null, new AssertAllowTransitions([['from' => '1', 'to' => ['2', '3']], ['from' => '2']]));
    }

    public function testThrowsWhenToIsNotInFrom() {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate(null, new AssertAllowTransitions([['from' => '1', 'to' => ['2']]]));
    }

    protected function createValidator(): AssertAllowTransitionsValidator {
        return new AssertAllowTransitionsValidator($this->requestStack);
    }
}

class TestClass {
    #[AssertAllowTransitions([
        ['from' => '1', 'to' => ['2', '3']],
        ['from' => '2', 'to' => ['3']],
        ['from' => '3', 'to' => ['1']],
    ])]
    public string $a;

    public function __construct($a) {
        $this->a = $a;
    }

    public function setA(string $a): TestClass {
        $this->a = $a;

        return $this;
    }
}
