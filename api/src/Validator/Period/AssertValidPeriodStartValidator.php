<?php

namespace App\Validator\Period;

use App\Entity\Period;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertValidPeriodStartValidator extends ConstraintValidator {
    public function __construct(private EntityManagerInterface $em) {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertValidPeriodStart) {
            throw new UnexpectedTypeException($constraint, AssertValidPeriodStart::class);
        }

        $period = $this->context->getObject();
        if (!$period instanceof Period) {
            throw new UnexpectedValueException($period, Period::class);
        }

        if (!$period->moveScheduleEntries && $period->scheduleEntries->count() > 0) {
            $delta = 0;
            $orig = $this->em->getUnitOfWork()->getOriginalEntityData($period);
            if (null != $orig) {
                $delta = $period->start->getTimestamp() - $orig['start']->getTimestamp();
                $delta = floor($delta / 60);

                // get minimal ScheduleEntryStart
                $scheduleEntryStarts = $period->scheduleEntries->map(fn ($se) => $se->periodOffset);
                $minScheduleEntryStart = min($scheduleEntryStarts->toArray());

                if ($minScheduleEntryStart < $delta) {
                    /** @var DateTime $startDate */
                    $startDate = clone $orig['start'];
                    $minScheduleEntryStartInDays = floor($minScheduleEntryStart / 1440);
                    $startDate->add(new DateInterval('P'.$minScheduleEntryStartInDays.'D'));

                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ startDate }}', $startDate->format('Y-m-d'))
                        ->addViolation()
                    ;
                }
            }
        }
    }
}
