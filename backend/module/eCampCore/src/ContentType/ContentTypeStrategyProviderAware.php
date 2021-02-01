<?php

namespace eCamp\Core\ContentType;

interface ContentTypeStrategyProviderAware {
    public function getContentTypeStrategyProvider(): ContentTypeStrategyProvider;

    public function setContentTypeStrategyProvider(ContentTypeStrategyProvider $contentTypeStrategyProvider);
}
