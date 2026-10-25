<?php

declare(strict_types=1);

namespace App\HttpCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Tells the HTTP cache which read access is required to be served a response, according to the security delegate that
 * the operation declares. Responses without a delegate must not be shared between users.
 */
final class CacheSecurityDelegateListener {
    public const string HEADER = 'X-Cache-Security-Delegate';
    public const string EXTRA_PROPERTY = 'cache_security_delegate';
    public const string DELEGATE_PUBLIC = 'public';

    public function __construct(private readonly CacheSecurityDelegateResolver $cacheSecurityDelegateResolver) {}

    public function onKernelResponse(ResponseEvent $event): void {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (!$request->isMethod(Request::METHOD_GET) || Response::HTTP_OK !== $response->getStatusCode()) {
            return;
        }

        $delegate = $this->cacheSecurityDelegateResolver->resolveDelegate($request);

        if (null !== $delegate) {
            $response->headers->set(self::HEADER, $delegate);
        }
    }
}
