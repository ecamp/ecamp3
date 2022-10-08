<?php

namespace App\Serializer\Normalizer\Error;

use Symfony\Component\Validator\ConstraintViolation;

class TranslationInfoOfConstraintViolation {
    public function extract(ConstraintViolation $constraintViolation): TranslationInfo {
        $constraint = $constraintViolation->getConstraint();
        $constraintClass = get_class($constraint);
        $key = str_replace('\\', '.', $constraintClass);
        $key = strtolower($key);
        $paramsWithoutCurlyBraces = self::removeCurlyBraces($constraintViolation->getParameters());

        return new TranslationInfo($key, $paramsWithoutCurlyBraces);
    }

    public static function removeCurlyBraces(array $parameters): array {
        $paramsWithoutCurlyBraces = [];
        foreach ($parameters as $key => $value) {
            $key = str_replace('{{ ', '', $key);
            $key = str_replace(' }}', '', $key);
            $paramsWithoutCurlyBraces[$key] = $value;
        }

        return $paramsWithoutCurlyBraces;
    }
}
