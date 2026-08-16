<?php

namespace App\Validator\PwnedPasswords;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NotPwnedPasswordValidator extends ConstraintValidator {
    private const LIST_PATH = __DIR__.'/password-list.txt';

    public function validate(mixed $value, Constraint $constraint): void {
        if (!$constraint instanceof NotPwnedPassword) {
            throw new UnexpectedTypeException($constraint, NotPwnedPassword::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $handle = fopen(self::LIST_PATH, 'rb');
        if (false === $handle) {
            throw new \RuntimeException('Unable to read the local password list.');
        }

        try {
            while (false !== ($line = fgets($handle))) {
                if (mb_strtolower(rtrim($line, "\r\n"), 'UTF-8') === mb_strtolower($value, 'UTF-8')) {
                    $this->context->buildViolation($constraint->message)
                        ->setCode(NotPwnedPassword::PWNED_ERROR)
                        ->addViolation()
                    ;

                    return;
                }
            }
        } finally {
            fclose($handle);
        }
    }
}
