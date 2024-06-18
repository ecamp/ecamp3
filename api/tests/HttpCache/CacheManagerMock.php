<?php

namespace App\Tests\HttpCache;

use FOS\HttpCacheBundle\CacheManager;

class CacheManagerMock extends CacheManager {
    private array $tags = [];

    public function __construct(
    ) {}

    public function flush(): int {
        return 0;
    }

    /**
     * @param string[] $tags Tags that should be removed/expired from the cache. An empty tag list is ignored.
     */
    public function invalidateTags(array $tags): static {
        if (!$tags) {
            return $this;
        }

        $this->tags = array_unique(array_merge($this->tags, $tags));

        return $this;
    }

    public function getInvalidatedTags(): array {
        return $this->tags;
    }
}
