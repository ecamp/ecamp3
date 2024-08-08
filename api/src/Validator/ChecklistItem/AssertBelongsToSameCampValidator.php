<?php

namespace App\Validator\ChecklistItem;

use App\Entity\ChecklistItem;
use App\Entity\ContentNode\ChecklistNode;
use App\Repository\ChecklistItemRepository;
use App\Util\GetCampFromContentNodeTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertBelongsToSameCampValidator extends ConstraintValidator {
    use GetCampFromContentNodeTrait;
    
    public function __construct(
        public RequestStack $requestStack, 
        private EntityManagerInterface $em,
        private ChecklistItemRepository $checklistItemRepository,
    ) {}

    public function validate($value, Constraint $constraint): void {
        if (!$constraint instanceof AssertBelongsToSameCamp) {
            throw new UnexpectedTypeException($constraint, AssertBelongsToSameCamp::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        $object = $this->context->getObject();
        if (!$object instanceof ChecklistNode) {
            throw new UnexpectedValueException($object, ChecklistNode::class);
        }

        $camp = $this->getCampFromInterface($object, $this->em);

        foreach($value as $checklistItemId) {
            /** @var ChecklistItem $checklistItem */
            $checklistItem = $this->checklistItemRepository->find($checklistItemId);

            if ($camp != $checklistItem?->getCamp()) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
