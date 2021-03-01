<?php

namespace eCamp\Core\ContentType;

use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Service\ServiceUtils;

abstract class ContentTypeStrategyBase implements ContentTypeStrategyInterface {
    private ServiceUtils $serviceUtils;

    public function __construct(ServiceUtils $serviceUtils) {
        $this->serviceUtils = $serviceUtils;
    }

    abstract public function contentNodeExtract(ContentNode $contentNode): array;

    abstract public function contentNodeCreated(ContentNode $contentNode): void;

    protected function getServiceUtils(): ServiceUtils {
        return $this->serviceUtils;
    }
}
