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

class AssertValidPeriodEndValidator extends ConstraintValidator {
    public function __construct(private EntityManagerInterface $em) {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertValidPeriodEnd) {
            throw new UnexpectedTypeException($constraint, AssertValidPeriodEnd::class);
        }

        $period = $this->context->getObject();
        if (!$period instanceof Period) {
            throw new UnexpectedValueException($period, Period::class);
        }

        if ($period->scheduleEntries->count() > 0) {
            $delta = 0;
            if (!$period->moveScheduleEntries) {
                $orig = $this->em->getUnitOfWork()->getOriginalEntityData($period);
                if (null != $orig) {
                    $delta = $period->start->getTimestamp() - $orig['start']->getTimestamp();
                    $delta = floor($delta / 60);
                }
            }

            // get maximal existing ScheduleEntryEnd
            $scheduleEntryEnds = $period->scheduleEntries->map(fn ($se) => $se->periodOffset + $se->length);
            $maxScheduleEntryEnd = max($scheduleEntryEnds->toArray());
            $periodEnd = 1440 * $period->getPeriodLength() + $delta;

            if ($maxScheduleEntryEnd > $periodEnd) {
                /** @var DateTime $endDate */
                $endDate = clone $period->end;
                $maxPeriodOffsetInDays = floor($maxScheduleEntryEnd / 1440);
                $endDate->add(new DateInterval('P'.$maxPeriodOffsetInDays.'D'));

                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ endDate }}', $endDate->format('Y-m-d'))
                    ->addViolation()
            ;
            }
        }
    }
}
