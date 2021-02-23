<?php

namespace eCamp\Core\ContentType;

use Doctrine\Persistence\Event\LifecycleEventArgs;

class ContentTypeStrategyProviderInjector {
    protected ContentTypeStrategyProvider $contentTypeStrategyProvider;

    public function __construct(ContentTypeStrategyProvider $contentTypeStrategyProvider) {
        $this->contentTypeStrategyProvider = $contentTypeStrategyProvider;
    }

    public function postLoad(LifecycleEventArgs $eventArgs): void {
        $this->inject($eventArgs);
    }

    public function prePersist(LifecycleEventArgs $eventArgs): void {
        $this->inject($eventArgs);
    }

    private function inject(LifecycleEventArgs $eventArgs): void {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof ContentTypeStrategyProviderAware) {
            $entity->setContentTypeStrategyProvider($this->contentTypeStrategyProvider);
        }
    }
}
