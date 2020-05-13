<?php

namespace eCamp\Core\ContentType;

interface ContentTypeStrategyProviderAware {
    /**
     * @return ContentTypeStrategyProvider
     */
    public function getContentTypeStrategyProvider();

    public function setContentTypeStrategyProvider(ContentTypeStrategyProvider $contentTypeStrategyProvider);
}
