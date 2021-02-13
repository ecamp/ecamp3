<?php

namespace eCamp\Core\ContentType;

use Doctrine\Persistence\Event\LifecycleEventArgs;

class ContentTypeStrategyProviderInjector {
    protected ContentTypeStrategyProvider $contentTypeStrategyProvider;

    public function __construct(ContentTypeStrategyProvider $contentTypeStrategyProvider) {
        $this->contentTypeStrategyProvider = $contentTypeStrategyProvider;
    }

    public function postLoad(LifecycleEventArgs $eventArgs) {
        $this->inject($eventArgs);
    }

    public function prePersist(LifecycleEventArgs $eventArgs) {
        $this->inject($eventArgs);
    }

    private function inject(LifecycleEventArgs $eventArgs) {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof ContentTypeStrategyProviderAware) {
            $entity->setContentTypeStrategyProvider($this->contentTypeStrategyProvider);
        }
    }
}
