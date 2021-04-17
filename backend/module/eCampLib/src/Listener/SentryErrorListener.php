<?php

namespace eCamp\Lib\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Mvc\MvcEvent;
use Sentry\State\HubInterface;

class SentryErrorListener implements ListenerAggregateInterface {
    use ListenerAggregateTrait;

    /** @var HubInterface */
    private $hub;

    public function __construct(HubInterface $hub) {
        $this->hub = $hub;
    }

    public function attach(EventManagerInterface $events, $priority = 1): void {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'handleError'], $priority);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'handleError'], $priority);
    }

    public function handleError(MvcEvent $event): void {
        $exception = $event->getParam('exception');
        if (!$exception instanceof \Throwable) {
            return;
        }

        $this->hub->captureException($exception);
    }
}
