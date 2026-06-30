<?php

namespace App\Security\PwnedPasswords;

use Symfony\Component\Validator\Constraint;

/**
 * Rejects passwords that are trivially derived from context-specific words
 * (application name, organisation, domain vocabulary).
 *
 * The word list is maintained in context-specific-words.txt in this directory.
 * OWASP ASVS 5.0 §6.1.2 / §6.2.11.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotContextSpecificPassword extends Constraint {
    public const CONTEXT_SPECIFIC_ERROR = 'e2a3c7d4-91f5-4b8e-a6b2-3d0f5e1c9a7f';

    protected const ERROR_NAMES = [
        self::CONTEXT_SPECIFIC_ERROR => 'CONTEXT_SPECIFIC_ERROR',
    ];

    public string $message = 'This password is too closely related to the application and cannot be used.';
}
