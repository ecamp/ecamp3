<?php

declare(strict_types=1);

namespace App\HttpCache;

use FOS\HttpCache\ProxyClient\HttpDispatcher;
use FOS\HttpCache\ProxyClient\Varnish;

/**
 * Wrapper around FOS\HttpCache\ProxyClient\Varnish
 * Implementing FOS\HttpCache\ProxyClient\Noop, if caching is disabled or no Varnish URL is set.
 */
final class VarnishProxyClient extends Varnish {
    private bool $apiCacheEnabled;

    public function __construct(string $apiCacheEnabled, private string $varnishApiUrl) {
        $this->apiCacheEnabled = filter_var($apiCacheEnabled, FILTER_VALIDATE_BOOLEAN);

        if ($this->isCacheEnabled()) {
            parent::__construct(
                new HttpDispatcher([$varnishApiUrl]),
                [
                    'tag_mode' => 'purgekeys',
                    'tags_header' => 'xkey-purge',
                ]
            );
        }
    }

    public function ban(array $headers): static {
        if ($this->isCacheEnabled()) {
            return parent::ban($headers);
        }

        return $this;
    }

    public function banPath($path, $contentType = null, $hosts = null): static {
        if ($this->isCacheEnabled()) {
            return parent::banPath($path, $contentType, $hosts);
        }

        return $this;
    }

    public function invalidateTags(array $tags): static {
        if ($this->isCacheEnabled()) {
            return parent::invalidateTags($tags);
        }

        return $this;
    }

    public function purge($url, array $headers = []): static {
        if ($this->isCacheEnabled()) {
            return parent::purge($url, $headers);
        }

        return $this;
    }

    public function refresh($url, array $headers = []): static {
        if ($this->isCacheEnabled()) {
            return parent::refresh(${$url}, $headers);
        }

        return $this;
    }

    public function flush(): int {
        if ($this->isCacheEnabled()) {
            return parent::flush();
        }

        return 0;
    }

    private function isCacheEnabled() {
        return $this->apiCacheEnabled && '' !== $this->varnishApiUrl;
    }
}
