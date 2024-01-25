<?php

declare(strict_types=1);

namespace App\HttpCache;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Disables cache and removed cache tags, if
 * - caching is disabled (configurable via environment variable API_CACHE_ENABLED)
 * - total header size is too large to be handled by Varnish (configurable via parameter app.httpCache.maxHeaderSize).
 */
final class CacheControlListener {
    private bool $apiCacheEnabled;

    private int $maxHeaderSize;

    public function __construct(string $apiCacheEnabled, string $maxHeaderSize, private string $headerKey = 'xkey') {
        $this->apiCacheEnabled = filter_var($apiCacheEnabled, FILTER_VALIDATE_BOOLEAN);
        $this->maxHeaderSize = intval($maxHeaderSize);
    }

    public function onKernelResponse(ResponseEvent $event): void {
        $response = $event->getResponse();
        $headerSize = strlen($response->headers->__toString());

        if (!$this->apiCacheEnabled || $headerSize > $this->maxHeaderSize) {
            $response->headers->remove('cache-control');
            $response->setCache(['no_cache' => true, 'private' => true]);

            $response->headers->remove($this->headerKey);

            return;
        }
    }
}
