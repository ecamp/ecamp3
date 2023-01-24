<?php

namespace App\Tests\Validator\Period;

use App\Entity\Period;
use App\Entity\ScheduleEntry;
use App\Validator\Period\AssertLessThanOrEqualToEarliestScheduleEntryStart;
use App\Validator\Period\AssertLessThanOrEqualToEarliestScheduleEntryStartValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertLessThanOrEqualToEarliestScheduleEntryStartValidatorTest extends ConstraintValidatorTestCase {
    public function testExpectesMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testNullIsValid() {
        $this->validator->validate(null, new AssertLessThanOrEqualToEarliestScheduleEntryStart());
        $this->assertNoViolation();
    }

    public function testEmptyIsValid() {
        $this->validator->validate('', new AssertLessThanOrEqualToEarliestScheduleEntryStart());
        $this->assertNoViolation();
    }

    public function testPeriodWithoutScheduleEntriesIsValid() {
        $period = new Period();

        $this->setObject($period);

        $this->validator->validate(new \DateTime(), new AssertLessThanOrEqualToEarliestScheduleEntryStart());
        $this->assertNoViolation();
    }

    public function testChangingPeriodStartMovingScheduleEntryIsValid() {
        // given
        $period = new Period();
        $period->moveScheduleEntries = true;

        $scheduleEntry = new ScheduleEntry();
        $scheduleEntry->startOffset = 300; // 2023-08-01 05:00
        $scheduleEntry->endOffset = 300 + 300; // 2023-08-01 10:00
        $period->addScheduleEntry($scheduleEntry);

        $this->setObject($period);

        // when
        $this->validator->validate(new \DateTime('2023-08-02'), new AssertLessThanOrEqualToEarliestScheduleEntryStart());
        // then
        $this->assertNoViolation();
    }

    public function testChangingPeriodStartNotMovingScheduleEntriesValidation() {
        // given
        // Orig-Start = 2023-08-01
        $period = new Period();
        $period->moveScheduleEntries = false;

        $scheduleEntry = new ScheduleEntry();
        $scheduleEntry->startOffset = 300; // 2023-08-01 05:00
        $scheduleEntry->endOffset = 300 + 300; // 2023-08-01 10:00
        $period->addScheduleEntry($scheduleEntry);

        $this->setObject($period);

        // when
        $this->validator->validate(new \DateTime('2023-07-31'), new AssertLessThanOrEqualToEarliestScheduleEntryStart());
        // then
        $this->assertNoViolation();

        // when
        $this->validator->validate(new \DateTime('2023-08-02'), new AssertLessThanOrEqualToEarliestScheduleEntryStart());
        // then
        $this->expectNoValidate();
    }

    protected function createValidator(): ConstraintValidatorInterface {
        /** @var MockObject|UnitOfWork $uow */
        $uow = $this->createMock(UnitOfWork::class);
        $uow->method('getOriginalEntityData')->willReturn([
            'start' => new \DateTime('2023-08-01'),
        ]);

        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getUnitOfWork')->willReturn($uow);

        return new AssertLessThanOrEqualToEarliestScheduleEntryStartValidator($em);
    }
}
