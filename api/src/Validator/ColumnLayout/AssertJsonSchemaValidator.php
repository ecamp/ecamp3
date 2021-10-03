<?php

namespace App\Validator\ColumnLayout;

use App\Entity\ContentNode\ColumnLayout;
use Swaggest\JsonSchema\Exception;
use Swaggest\JsonSchema\InvalidValue;
use Swaggest\JsonSchema\Schema;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertJsonSchemaValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertJsonSchema) {
            throw new UnexpectedTypeException($constraint, AssertJsonSchema::class);
        }

        $schema = $constraint->schema;

        /*
        $object = $this->context->getObject();

        if (!($object instanceof ColumnLayout)) {
            throw new InvalidArgumentException('AssertJsonSchemaValidator is only valid inside a ColumnLayout object');
        }*/

        try {
            // Re-encode and decode the schema value, because the schema checker needs
            // objects to be represented as stdObjects
            $schemaChecker = Schema::import(json_decode(json_encode($schema)));

            // Re-encode and decode the input value, because the schema checker needs
            // objects to be represented as stdObjects
            $schemaChecker->in(json_decode(json_encode($value)));
        } catch (InvalidValue|Exception $exception) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    /*
    protected function validateColumWidthsSumTo12(ContentNode $contentNode) {
        $columnWidths = array_sum(array_map(function ($col) {
            return $col['width'];
        }, $contentNode->getJsonConfig()['columns']));
        if (12 !== $columnWidths) {
            throw (new EntityValidationException())->setMessages([
                'jsonConfig' => [
                    'invalidWidths' => 'Expected column widths to sum to 12, but got a sum of '.$columnWidths,
                ],
            ]);
        }
    }

    protected function validateNoOrphanChildren(ContentNode $contentNode) {
        $slots = array_map(function ($col) {
            return $col['slot'];
        }, $contentNode->getJsonConfig()['columns']);
        $childSlots = $contentNode->getChildren()->map(function (ContentNode $child) {
            return $child->getSlot();
        })->toArray();
        $orphans = array_diff($childSlots, $slots);

        if (count($orphans)) {
            throw (new EntityValidationException())->setMessages([
                'jsonConfig' => [
                    'orphanChildContents' => 'The following slots still have child contents and should be present in the columns: '.join(', ', $orphans),
                ],
            ]);
        }
    }*/
}
