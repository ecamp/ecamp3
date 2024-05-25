<?php

declare(strict_types=1);

namespace App\HttpCache;

use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Wrapper around SymfonyResponseTagger which only adds tags for specific URIs, which match the regex $matchPath.
 *
 * @author Urban Suppiger <urban@suppiger.net>
 */
class ResponseTagger {
    public function __construct(
        private string $matchPath,
        private SymfonyResponseTagger $responseTagger,
        private RequestStack $requestStack
    ) {}

    /**
     * Add tags to be set on the response.
     *
     * Only adds tags for requests that are cacheable
     *
     * @param string[] $tags List of tags to add
     */
    public function addTags(array $tags) {
        if ($this->isCacheable()) {
            $this->responseTagger->addTags($tags);
        }
    }

    private function isCacheable(): bool {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request->isMethodCacheable()) {
            return false;
        }

        $requestUri = $request->getRequestUri();

        return (bool) preg_match('{'.$this->matchPath.'}', $requestUri);
    }
}
