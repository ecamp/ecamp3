<?php

namespace App\Tests\Validator\Period;

use App\Entity\Period;
use App\Entity\ScheduleEntry;
use App\Validator\Period\AssertGreaterThanOrEqualToLastScheduleEntryEnd;
use App\Validator\Period\AssertGreaterThanOrEqualToLastScheduleEntryEndValidator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertGreaterThanOrEqualToLastScheduleEntryEndValidatorTest extends ConstraintValidatorTestCase {
    public function testExpectesMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testNullIsValid() {
        $this->validator->validate(null, new AssertGreaterThanOrEqualToLastScheduleEntryEnd());
        $this->assertNoViolation();
    }

    public function testEmptyIsValid() {
        $this->validator->validate('', new AssertGreaterThanOrEqualToLastScheduleEntryEnd());
        $this->assertNoViolation();
    }

    public function testPeriodWithoutScheduleEntriesIsValid() {
        $period = new Period();
        $period->start = new DateTime();

        $this->setObject($period);

        $this->validator->validate(new DateTime(), new AssertGreaterThanOrEqualToLastScheduleEntryEnd());
        $this->assertNoViolation();
    }

    public function testPeriodEndValidation() {
        // given
        $period = new Period();
        $period->moveScheduleEntries = true;
        $period->start = new DateTime('2023-08-01');

        $scheduleEntry = new ScheduleEntry();
        $scheduleEntry->periodOffset = 3600; // 2023-08-03 12:00
        $scheduleEntry->length = 300; // 5h
        $period->addScheduleEntry($scheduleEntry);

        $this->setObject($period);

        // when
        $this->validator->validate(new DateTime('2023-08-03'), new AssertGreaterThanOrEqualToLastScheduleEntryEnd());
        // then
        $this->assertNoViolation();

        // when
        $this->validator->validate(new DateTime('2023-08-02'), new AssertGreaterThanOrEqualToLastScheduleEntryEnd());
        // then
        $this->expectNoValidate();
    }

    public function testChangingPeriodStartMovingScheduleEntryPeriodEndValidation() {
        // given
        // Orig-Start = 2023-08-01
        // New-Start  = 2023-07-31
        $period = new Period();
        $period->moveScheduleEntries = true;
        $period->start = new DateTime('2023-07-31');

        $scheduleEntry = new ScheduleEntry();
        $scheduleEntry->periodOffset = 3600; // New: 2023-08-02 12:00
        $scheduleEntry->length = 300; // 5h
        $period->addScheduleEntry($scheduleEntry);

        $this->setObject($period);

        // when
        $this->validator->validate(new DateTime('2023-08-02'), new AssertGreaterThanOrEqualToLastScheduleEntryEnd());
        // then
        $this->assertNoViolation();

        // when
        $this->validator->validate(new DateTime('2023-08-01'), new AssertGreaterThanOrEqualToLastScheduleEntryEnd());
        // then
        $this->expectNoValidate();
    }

    public function testChangingPeriodStartNotMovingScheduleEntriesPeriodEndValidation() {
        // given
        // Orig-Start = 2023-08-01
        // New-Start  = 2023-07-31
        $period = new Period();
        $period->moveScheduleEntries = false;
        $period->start = new DateTime('2023-07-31');

        $scheduleEntry = new ScheduleEntry();
        $scheduleEntry->periodOffset = 3600; // 2023-08-03 12:00
        $scheduleEntry->length = 300; // 5h
        $period->addScheduleEntry($scheduleEntry);

        $this->setObject($period);

        // when
        $this->validator->validate(new DateTime('2023-08-03'), new AssertGreaterThanOrEqualToLastScheduleEntryEnd());
        // then
        $this->assertNoViolation();

        // when
        $this->validator->validate(new DateTime('2023-08-02'), new AssertGreaterThanOrEqualToLastScheduleEntryEnd());
        // then
        $this->expectNoValidate();
    }

    protected function createValidator() {
        /** @var MockObject|UnitOfWork $uow */
        $uow = $this->createMock(UnitOfWork::class);
        $uow->method('getOriginalEntityData')->willReturn([
            'start' => new DateTime('2023-08-01'),
        ]);

        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getUnitOfWork')->willReturn($uow);

        return new AssertGreaterThanOrEqualToLastScheduleEntryEndValidator($em);
    }
}
