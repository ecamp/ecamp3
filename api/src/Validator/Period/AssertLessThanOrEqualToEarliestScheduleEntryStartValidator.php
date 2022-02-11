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

class AssertLessThanOrEqualToEarliestScheduleEntryStartValidator extends ConstraintValidator {
    public function __construct(private EntityManagerInterface $em) {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertLessThanOrEqualToEarliestScheduleEntryStart) {
            throw new UnexpectedTypeException($constraint, AssertLessThanOrEqualToEarliestScheduleEntryStart::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $period = $this->context->getObject();
        if (!$period instanceof Period) {
            throw new UnexpectedValueException($period, Period::class);
        }

        if (!$period->moveScheduleEntries && $period->scheduleEntries->count() > 0) {
            $orig = $this->em->getUnitOfWork()->getOriginalEntityData($period);
            if (null != $orig) {
                /** @var DateTime $origPeriodStart */
                $origPeriodStart = clone $orig['start'];
                $firstScheduleEntryPeriodOffset = min($period->scheduleEntries->map(fn ($se) => $se->periodOffset)->toArray());
                $firstScheduleEntryStart = $origPeriodStart->add(new DateInterval('PT'.$firstScheduleEntryPeriodOffset.'M'));

                if ($firstScheduleEntryStart < $value) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ startDate }}', $firstScheduleEntryStart->format('Y-m-d'))
                        ->addViolation()
                    ;
                }
            }
        }
    }
}
