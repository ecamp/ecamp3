<?php

namespace App\Validator\Period;

use App\Entity\BelongsToCampInterface;
use App\Entity\Period;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertNotOverlappingWithOtherPeriodsValidator extends ConstraintValidator {
    public function validate(mixed $value, Constraint $constraint) {
        if (!$constraint instanceof AssertNotOverlappingWithOtherPeriods) {
            throw new UnexpectedTypeException($constraint, AssertNotOverlappingWithOtherPeriods::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof \DateTimeInterface) {
            throw new UnexpectedValueException($value, \DateTimeInterface::class);
        }

        $object = $this->context->getObject();
        if (!$object instanceof BelongsToCampInterface) {
            throw new UnexpectedTypeException($object, BelongsToCampInterface::class);
        }

        $camp = $object->getCamp();
        if (null === $camp) {
            return;
        }
        $periods = $camp->getPeriods();
        $overlappingExists = (new ArrayCollection($periods))
            ->filter(fn (Period $p) => $p !== $object)
            ->exists(fn ($_, Period $p) => self::overlaps($value, $p))
        ;
        if ($overlappingExists) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    private static function overlaps(\DateTimeInterface $date, Period $period): bool {
        return $date->getTimestamp() >= $period->start->getTimestamp()
            && $date->getTimestamp() <= $period->end->getTimestamp();
    }
}
