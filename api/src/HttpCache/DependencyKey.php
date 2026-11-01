<?php

declare(strict_types=1);

namespace App\HttpCache;

use App\Entity\HasId;

/**
 * Cache tag for values that are derived from several entities. The names are defined by the entity that exposes the derived value.
 * Responses containing such a value are tagged with the key, changes to any input of the derivation purge it.
 */
final class DependencyKey {
    public const string DELIMITER = '^';

    public static function of(HasId $entity, string $name): string {
        return $entity->getId().self::DELIMITER.$name;
    }
}
