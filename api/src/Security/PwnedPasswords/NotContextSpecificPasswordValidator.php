<?php

namespace App\Security\PwnedPasswords;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NotContextSpecificPasswordValidator extends ConstraintValidator {
    private const WORDS_PATH = __DIR__.'/context-specific-words.txt';

    /** @var null|string[] */
    private ?array $words = null;

    public function validate(mixed $value, Constraint $constraint): void {
        if (!$constraint instanceof NotContextSpecificPassword) {
            throw new UnexpectedTypeException($constraint, NotContextSpecificPassword::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if ($this->isContextSpecific($value)) {
            $this->context->buildViolation($constraint->message)
                ->setCode(NotContextSpecificPassword::CONTEXT_SPECIFIC_ERROR)
                ->addViolation()
            ;
        }
    }

    private function isContextSpecific(string $password): bool {
        if (null === $this->words) {
            $lines = file(self::WORDS_PATH, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES);
            $this->words = array_map('strtolower', $lines ?: []);
        }

        $lower = strtolower($password);

        foreach ($this->words as $word) {
            if (str_contains($lower, $word)) {
                return true;
            }
        }

        return false;
    }
}
