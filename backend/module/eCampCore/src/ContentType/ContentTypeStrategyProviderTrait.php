<?php

namespace eCamp\Core\ContentType;

trait ContentTypeStrategyProviderTrait {
    private ContentTypeStrategyProvider $contentTypeStrategyProvider;

    public function setContentTypeStrategyProvider(ContentTypeStrategyProvider $contentTypeStrategyProvider): void {
        $this->contentTypeStrategyProvider = $contentTypeStrategyProvider;
    }

    public function getContentTypeStrategyProvider(): ContentTypeStrategyProvider {
        return $this->contentTypeStrategyProvider;
    }
}
