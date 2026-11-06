<?php

declare(strict_types=1);

namespace App\HttpCache;

use App\Entity\HasId;

/**
 * Format of the cache tags that are added to responses and purged on changes.
 * Besides the plain entity id, a response is tagged with the relations of an entity it contains,
 * and with custom tags for values that are derived from several entities.
 */
final class CacheTag {
    public const string RELATION_TAG_DELIMITER = '#';
    public const string CUSTOM_TAG_DELIMITER = '^';

    public static function forRelation(string $id, string $relation): string {
        return $id.self::RELATION_TAG_DELIMITER.$relation;
    }

    public static function forCustomTag(HasId $entity, string $tagName): string {
        return $entity->getId().self::CUSTOM_TAG_DELIMITER.$tagName;
    }
}
