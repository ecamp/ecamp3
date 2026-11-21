<?php

declare(strict_types=1);

namespace App\Security;

use ApiPlatform\Metadata\HttpOperation;

/**
 * Result of checking whether the current user may read (GET) a resource IRI.
 */
final readonly class ResourceReadAccess {
    public function __construct(
        public bool $granted,
        public string $iri,
        public string $resourceClass,
        public HttpOperation $operation,
    ) {}
}
