<?php

namespace App\Validator\Period;

use App\Entity\Period;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertGreaterThanOrEqualToLastScheduleEntryEndValidator extends ConstraintValidator {
    public function __construct(private EntityManagerInterface $em) {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertGreaterThanOrEqualToLastScheduleEntryEnd) {
            throw new UnexpectedTypeException($constraint, AssertGreaterThanOrEqualToLastScheduleEntryEnd::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        /** @var \DateTime $periodEnd */
        $periodEnd = clone $value;
        // Period ends at the end of the day (+1day)
        $periodEnd->add(new \DateInterval('P1D'));

        $period = $this->context->getObject();
        if (!$period instanceof Period) {
            throw new UnexpectedValueException($period, Period::class);
        }

        if ($period->scheduleEntries->count() > 0) {
            /** @var \DateTime $periodStart */
            $periodStart = $period->start;

            if (!$period->moveScheduleEntries) {
                $orig = $this->em->getUnitOfWork()->getOriginalEntityData($period);
                if (null != $orig) {
                    $periodStart = $orig['start'];
                }
            }

            $periodStart = clone $periodStart;
            $lastScheduleEntryPeriodEndOffset = max($period->scheduleEntries->map(fn ($se) => $se->endOffset)->toArray());
            $lastScheduleEntryEnd = $periodStart->add(new \DateInterval('PT'.$lastScheduleEntryPeriodEndOffset.'M'));

            if ($periodEnd < $lastScheduleEntryEnd) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ endDate }}', $lastScheduleEntryEnd->format('Y-m-d'))
                    ->addViolation()
                ;
            }
        }
    }
}
