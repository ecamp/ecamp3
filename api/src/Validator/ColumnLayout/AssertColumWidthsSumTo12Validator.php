<?php

namespace App\Validator\ColumnLayout;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertColumWidthsSumTo12Validator extends ConstraintValidator {
    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertColumWidthsSumTo12) {
            throw new UnexpectedTypeException($constraint, AssertColumWidthsSumTo12::class);
        }

        $columnWidths = array_sum(array_map(function ($col) {
            if (isset($col['width'])) {
                return $col['width'];
            }

            return 0;
        }, $value['columns']));

        if (12 !== $columnWidths) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ sum }}', strval($columnWidths))
                ->addViolation()
            ;
        }
    }
}
