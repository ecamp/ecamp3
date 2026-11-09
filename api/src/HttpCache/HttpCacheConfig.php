<?php

declare(strict_types=1);

namespace App\HttpCache;

use ApiPlatform\Metadata\Operation;

/**
 * How the HTTP cache treats the responses of an operation, declared in its extra property "http_cache":
 * - true or ['mode' => 'user']: cached separately for every login
 * - ['mode' => 'shared', 'security_delegate' => 'camp']: shared between all users who may read the resource that the
 *   expression evaluates to. Like the "security" expression of an operation, it can use the variable "object" for the
 *   resource of an item operation, or the linked resources of a subresource collection operation by the name used in
 *   their link, e.g. "camp".
 * - ['mode' => 'public']: shared with everyone
 * Operations without the property, or with false, are not cached.
 */
final readonly class HttpCacheConfig {
    public const string EXTRA_PROPERTY = 'http_cache';

    private const array KEYS = ['mode', 'security_delegate'];

    private function __construct(
        public CacheMode $mode,
        public ?string $securityDelegate = null,
    ) {}

    public static function public(): self {
        return new self(CacheMode::Public);
    }

    /**
     * @throws \LogicException if the declaration is invalid
     */
    public static function fromOperation(Operation $operation): ?self {
        $declared = $operation->getExtraProperties()[self::EXTRA_PROPERTY] ?? null;

        if (null === $declared || false === $declared) {
            return null;
        }

        if (true === $declared) {
            return new self(CacheMode::User);
        }

        if (!is_array($declared)) {
            throw new \LogicException("{$operation->getName()}: http_cache must be true or an array.");
        }

        $unknownKeys = array_diff(array_keys($declared), self::KEYS);
        if ([] !== $unknownKeys) {
            throw new \LogicException("{$operation->getName()}: unknown http_cache options: ".implode(', ', $unknownKeys).'.');
        }

        $mode = CacheMode::tryFrom($declared['mode'] ?? CacheMode::User->value);
        if (null === $mode) {
            throw new \LogicException("{$operation->getName()}: unknown http_cache mode.");
        }

        $securityDelegate = $declared['security_delegate'] ?? null;
        if (CacheMode::Shared === $mode) {
            if (!is_string($securityDelegate) || '' === $securityDelegate) {
                throw new \LogicException("{$operation->getName()}: http_cache mode shared requires a security_delegate.");
            }
        } elseif (null !== $securityDelegate) {
            throw new \LogicException("{$operation->getName()}: http_cache mode {$mode->value} must not have a security_delegate.");
        }

        return new self($mode, $securityDelegate);
    }
}
