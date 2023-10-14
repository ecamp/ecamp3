<?php

namespace App\Validator\ColumnLayout;

use App\Entity\ContentNode;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\FlexLayout;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertNoOrphanChildrenValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint): void {
        if (!$constraint instanceof AssertNoOrphanChildren) {
            throw new UnexpectedTypeException($constraint, AssertNoOrphanChildren::class);
        }

        /** @var ColumnLayout|FlexLayout $layout */
        $layout = $this->context->getObject();

        if ($layout instanceof ColumnLayout) {
            if (!isset($value['columns'])) {
                throw new \TypeError('Property "columns" expected but not found');
            }

            $array = $value['columns'];
        } elseif ($layout instanceof FlexLayout) {
            if (!isset($value['items'])) {
                throw new \TypeError('Property "items" expected but not found');
            }

            $array = $value['items'];
        } else {
            throw new InvalidArgumentException('AssertNoOrphanChildren is only valid inside ColumnLayout or FlexLayout object');
        }

        $slots = array_map(function ($col) {
            if (isset($col['slot'])) {
                return $col['slot'];
            }

            return null;
        }, $array);

        $childSlots = $layout->children->map(function (ContentNode $child) {
            return $child->slot;
        })->toArray();

        $orphans = array_unique(array_diff($childSlots, $slots));

        if (count($orphans)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ slots }}', join(', ', $orphans))
                ->addViolation()
            ;
        }
    }
}
