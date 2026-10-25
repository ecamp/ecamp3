<?php

declare(strict_types=1);

namespace App\HttpCache;

use App\Controller\CacheAccessController;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Tags responses with the access rules (e.g. a camp collaboration) that allowed to read them, such that the cache can
 * purge them when the access is revoked.
 *
 * Responses of operations with a cache scope are shared between users, access to them is checked by the cache access
 * endpoint. Therefore only the cache access endpoint is tagged for these.
 */
class AccessTagger {
    public function __construct(
        private readonly ResponseTagger $responseTagger,
        private readonly SymfonyResponseTagger $symfonyResponseTagger,
        private readonly CacheScopeResolver $cacheScopeResolver,
        private readonly RequestStack $requestStack,
    ) {}

    /**
     * @param string[] $tags
     */
    public function addTags(array $tags): void {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        if (CacheAccessController::ROUTE === $request->attributes->get('_route')) {
            $this->symfonyResponseTagger->addTags($tags);

            return;
        }

        if (null === $this->cacheScopeResolver->getDeclaredScope($request)) {
            $this->responseTagger->addTags($tags);
        }
    }
}
