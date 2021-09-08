<?php

namespace App\Tests\Validator;

use App\Entity\BaseEntity;
use App\Entity\BelongsToCampInterface;
use App\Entity\Camp;
use App\Validator\AssertBelongsToSameCamp;
use App\Validator\AssertBelongsToSameCampValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertBelongsToSameCampValidatorTest extends ConstraintValidatorTestCase {
    private MockObject|RequestStack $requestStack;

    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testExpectsBelongsToCampValue() {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(new \stdClass(), new AssertBelongsToSameCamp());
    }

    public function testExpectsBelongsToCampObject() {
        // given
        $camp = $this->createMock(Camp::class);
        $camp->method('getId')->willReturn('idfromtest');
        $child = new ChildTestClass($camp);

        $this->setObject(new \stdClass());

        // then
        $this->expectException(UnexpectedValueException::class);

        // when
        $this->validator->validate($child, new AssertBelongsToSameCamp());
    }

    public function testNullIsValid() {
        $this->validator->validate(null, new AssertBelongsToSameCamp());
        $this->assertNoViolation();
    }

    public function testEmptyIsValid() {
        $this->validator->validate('', new AssertBelongsToSameCamp());
        $this->assertNoViolation();
    }

    public function testValid() {
        // given
        $camp = $this->createMock(Camp::class);
        $camp->method('getId')->willReturn('idfromtest');
        $child = new ChildTestClass($camp);
        $parent = new ParentTestClass($camp, $child);
        $this->setObject($parent);

        // when
        $this->validator->validate($child, new AssertBelongsToSameCamp());

        // then
        $this->assertNoViolation();
    }

    public function testInvalid() {
        // given
        $camp = $this->createMock(Camp::class);
        $camp2 = $this->createMock(Camp::class);
        $camp->method('getId')->willReturn('idfromtest');
        $child = new ChildTestClass($camp2);
        $parent = new ParentTestClass($camp, $child);
        $this->setObject($parent);

        // when
        $this->validator->validate($child, new AssertBelongsToSameCamp());

        // then
        $this->buildViolation('Must belong to the same camp.')->assertRaised();
    }

    public function testCompareToPreviousValid() {
        // given
        $camp = $this->createMock(Camp::class);
        $camp2 = $this->createMock(Camp::class);
        $camp->method('getId')->willReturn('idfromtest');
        $child = new ChildTestClass($camp);
        $parent = new ParentTestClass($camp2, $child);
        $this->setObject($parent);

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['previous_data' => new ParentTestClass($camp, $child)]);
        $this->requestStack->method('getCurrentRequest')->willReturn($request);

        // when
        $this->validator->validate($child, new AssertBelongsToSameCamp(null, true));

        // then
        $this->assertNoViolation();
    }

    public function testCompareToPreviousInvalid() {
        // given
        $camp = $this->createMock(Camp::class);
        $camp2 = $this->createMock(Camp::class);
        $camp->method('getId')->willReturn('idfromtest');
        $child = new ChildTestClass($camp);
        $parent = new ParentTestClass($camp, $child);
        $this->setObject($parent);

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['previous_data' => new ParentTestClass($camp2, $child)]);
        $this->requestStack->method('getCurrentRequest')->willReturn($request);

        // when
        $this->validator->validate($child, new AssertBelongsToSameCamp(null, true));

        // then
        $this->buildViolation('Must belong to the same camp.')->assertRaised();
    }

    protected function createValidator() {
        $this->requestStack = $this->createMock(RequestStack::class);

        return new AssertBelongsToSameCampValidator($this->requestStack);
    }
}

class ChildTestClass implements BelongsToCampInterface {
    public function __construct(public Camp $camp) {
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }
}

class ParentTestClass extends BaseEntity implements BelongsToCampInterface {
    #[AssertBelongsToSameCamp]
    public ChildTestClass $child;

    public function __construct(public Camp $camp, ChildTestClass $child) {
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }
}
