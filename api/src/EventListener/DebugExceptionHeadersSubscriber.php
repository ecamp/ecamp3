<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class DebugExceptionHeadersSubscriber implements EventSubscriberInterface {
    public function __construct(private string $env) {}

    public static function getSubscribedEvents(): array {
        return [KernelEvents::EXCEPTION => ['onException', -256]];
    }

    public function onException(ExceptionEvent $event): void {
        if ('e2e' !== $this->env) {
            return;
        }

        $response = $event->getResponse();
        if (null === $response) {
            return;
        }

        $throwable = $event->getThrowable();
        $response->headers->set('X-Debug-Exception', rawurlencode(substr($throwable->getMessage(), 0, 2000)));
        $response->headers->set('X-Debug-Exception-File', rawurlencode($throwable->getFile()).':'.$throwable->getLine());
    }
}
