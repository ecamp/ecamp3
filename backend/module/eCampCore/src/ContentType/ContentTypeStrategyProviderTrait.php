<?php

namespace eCamp\Core\ContentType;

trait ContentTypeStrategyProviderTrait {
    /** @var ContentTypeStrategyProvider */
    private $contentTypeStrategyProvider;

    public function setContentTypeStrategyProvider(ContentTypeStrategyProvider $contentTypeStrategyProvider) {
        $this->contentTypeStrategyProvider = $contentTypeStrategyProvider;
    }

    public function getContentTypeStrategyProvider() {
        return $this->contentTypeStrategyProvider;
    }
}
