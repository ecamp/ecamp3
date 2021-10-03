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

        $object = $this->context->getObject();

        // validate prototype if provided (and $value empty)
        $columns = $value ?? $object->prototype->columns;

        $columnWidths = array_sum(array_map(function ($col) {
            return $col['width'];
        }, $columns));

        if (12 !== $columnWidths) {
            $this->context->buildViolation(sprintf($constraint->message, $columnWidths))->addViolation();
        }
    }
}
