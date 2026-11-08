<?php

declare(strict_types=1);

namespace App\HttpCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Allows the HTTP cache to store successful responses of operations that declare to be cached, and tells it how.
 * Responses of the current login are only cached for the lifetime of its JWT.
 */
final class CacheableResponseListener {
    public const string HEADER_MODE = 'X-Cache-Mode';
    public const string HEADER_SECURITY_DELEGATE = 'X-Cache-Security-Delegate';

    public function __construct(
        private readonly HttpCacheConfigResolver $configResolver,
        private readonly CacheSecurityDelegateResolver $securityDelegateResolver,
        private readonly JwtLifetime $jwtLifetime,
    ) {}

    public function onKernelResponse(ResponseEvent $event): void {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (!$request->isMethod(Request::METHOD_GET) || Response::HTTP_OK !== $response->getStatusCode()) {
            return;
        }

        $config = $this->configResolver->getConfig($request);
        if (null === $config) {
            return;
        }

        $ttl = CacheMode::User === $config->mode ? $this->jwtLifetime->getCacheTtl() : JwtLifetime::MAX_TTL_SECONDS;
        if ($ttl <= 0) {
            return;
        }

        if (CacheMode::Shared === $config->mode) {
            $delegate = $this->securityDelegateResolver->resolveDelegate($request, $config);
            if (null === $delegate) {
                return;
            }
            $response->headers->set(self::HEADER_SECURITY_DELEGATE, $delegate);
        }

        $response->headers->set(self::HEADER_MODE, $config->mode->value);
        $response->headers->set('Cache-Control', "public, max-age=0, s-maxage={$ttl}");
        $response->headers->set('Vary', 'Accept, Content-Type, Authorization, Origin');
    }
}
