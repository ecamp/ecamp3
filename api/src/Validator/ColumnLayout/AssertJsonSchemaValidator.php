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
}
