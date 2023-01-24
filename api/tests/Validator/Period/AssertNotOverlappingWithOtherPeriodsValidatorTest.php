<?php

namespace App\Tests\Validator\Period;

use App\Entity\Camp;
use App\Entity\Period;
use App\Validator\Period\AssertNotOverlappingWithOtherPeriods;
use App\Validator\Period\AssertNotOverlappingWithOtherPeriodsValidator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertNotOverlappingWithOtherPeriodsValidatorTest extends ConstraintValidatorTestCase {
    private Period $period1;
    private \DateTime $period1Start;

    private Period $period2;
    private \DateTime $period2Start;

    private \DateTime $period3End;

    protected function setUp(): void {
        parent::setUp();
        $this->period1Start = new \DateTime('2022-01-01');
        $period1End = new \DateTime('2022-01-05');
        $this->period1 = new Period();
        $this->period1->start = $this->period1Start;
        $this->period1->end = $period1End;

        $this->period2Start = new \DateTime('2022-01-07');
        $period2End = new \DateTime('2022-01-12');
        $this->period2 = new Period();
        $this->period2->start = $this->period2Start;
        $this->period2->end = $period2End;

        $period3Start = new \DateTime('2022-01-15');
        $this->period3End = new \DateTime('2022-01-20');
        $period3 = new Period();
        $period3->start = $period3Start;
        $period3->end = $this->period3End;

        $camp = new Camp();
        $camp->addPeriod($this->period1);
        $camp->addPeriod($this->period2);
        $camp->addPeriod($period3);
    }

    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate(null, new Email());
    }

    public function testExpectsDateTimeInterfaceValue() {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate('', new AssertNotOverlappingWithOtherPeriods());
    }

    public function testAllowsNullValue() {
        $this->setObject($this->period1);

        $this->validator->validate(null, new AssertNotOverlappingWithOtherPeriods());

        $this->assertNoViolation();
    }

    public function testExpectsObjectToHaveCampProperty() {
        $this->setObject(new \stdClass());
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate(new \DateTime(), new AssertNotOverlappingWithOtherPeriods());
    }

    public function testAllowsNullCamp() {
        $this->period1->camp = null;
        $this->setObject($this->period1);

        $this->validator->validate($this->period2->start, new AssertNotOverlappingWithOtherPeriods());

        $this->assertNoViolation();
    }

    public function testAllowsNotOverlappingPeriods() {
        $this->setObject($this->period1);

        $this->validator->validate($this->period1Start, new AssertNotOverlappingWithOtherPeriods());

        $this->assertNoViolation();
    }

    public function testAllowsNotOverlappingPeriods2() {
        $this->setObject($this->period2);

        $this->validator->validate($this->period2Start, new AssertNotOverlappingWithOtherPeriods());

        $this->assertNoViolation();
    }

    public function testRejectsOverlappingPeriods() {
        $this->setObject($this->period1);

        $dateTime = $this->period2Start->add(new \DateInterval('P1D'));
        $this->validator->validate($dateTime, new AssertNotOverlappingWithOtherPeriods());

        $this->buildViolation(AssertNotOverlappingWithOtherPeriods::DEFAULT_MESSAGE)->assertRaised();
    }

    public function testRejectsOverlappingPeriods2() {
        $this->setObject($this->period2);

        $dateTime = $this->period1Start->add(new \DateInterval('P1D'));
        $this->validator->validate($dateTime, new AssertNotOverlappingWithOtherPeriods());

        $this->buildViolation(AssertNotOverlappingWithOtherPeriods::DEFAULT_MESSAGE)->assertRaised();
    }

    public function testRejectsOverlappingPeriodsIfHitsPeriodStart() {
        $this->setObject($this->period1);

        $this->validator->validate($this->period2Start, new AssertNotOverlappingWithOtherPeriods());

        $this->buildViolation(AssertNotOverlappingWithOtherPeriods::DEFAULT_MESSAGE)->assertRaised();
    }

    public function testRejectsOverlappingPeriodsIfHitsPeriodEnd() {
        $this->setObject($this->period1);

        $this->validator->validate($this->period3End, new AssertNotOverlappingWithOtherPeriods());

        $this->buildViolation(AssertNotOverlappingWithOtherPeriods::DEFAULT_MESSAGE)->assertRaised();
    }

    protected function createValidator(): AssertNotOverlappingWithOtherPeriodsValidator {
        return new AssertNotOverlappingWithOtherPeriodsValidator();
    }
}
