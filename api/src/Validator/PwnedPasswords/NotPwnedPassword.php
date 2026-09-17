<?php

namespace App\Validator\PwnedPasswords;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotPwnedPassword extends Constraint {
    public const PWNED_ERROR = 'bb2dba73-b6e5-4f7e-bdc3-6d0f12f20e83';

    protected const ERROR_NAMES = [
        self::PWNED_ERROR => 'PWNED_ERROR',
    ];

    public function __construct(
        public readonly string $message = 'This password has appeared in a data breach and cannot be used. Please choose a different password.',
        ?array $groups = null,
        $payload = null
    ) {
        parent::__construct(null, $groups, $payload);
    }
}
