<?php

declare(strict_types=1);

namespace App\HttpCache;

use App\Controller\CheckAccessController;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Tags responses with the access rules (e.g. a camp collaboration) that allowed to read them, such that the cache can
 * purge them when the access is revoked.
 *
 * Responses shared between users are tagged on the check access endpoint only, as access to them is checked there.
 */
class SecurityVoterTagger {
    public function __construct(
        private readonly ResponseTagger $responseTagger,
        private readonly SymfonyResponseTagger $symfonyResponseTagger,
        private readonly HttpCacheConfigResolver $configResolver,
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

        if (CheckAccessController::ROUTE === $request->attributes->get('_route')) {
            $this->symfonyResponseTagger->addTags($tags);

            return;
        }

        if (CacheMode::Shared !== $this->configResolver->getConfig($request)?->mode) {
            $this->responseTagger->addTags($tags);
        }
    }
}
