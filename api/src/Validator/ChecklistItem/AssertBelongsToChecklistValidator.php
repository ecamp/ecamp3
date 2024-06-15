<?php

namespace App\Validator\ChecklistItem;

use App\Entity\ChecklistItem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertBelongsToChecklistValidator extends ConstraintValidator {
    public function __construct(public RequestStack $requestStack) {}

    public function validate($value, Constraint $constraint): void {
        if (!$constraint instanceof AssertBelongsToChecklist) {
            throw new UnexpectedTypeException($constraint, AssertBelongsToChecklist::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof ChecklistItem) {
            throw new UnexpectedValueException($value, ChecklistItem::class);
        }

        $object = $this->context->getObject();
        if (!$object instanceof ChecklistItem) {
            throw new UnexpectedValueException($object, ChecklistItem::class);
        }

        if ($value->checklist->getId() !== $object->checklist->getId()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
