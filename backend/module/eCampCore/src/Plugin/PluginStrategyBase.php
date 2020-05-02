<?php

namespace eCamp\Core\Plugin;

use eCamp\Core\Entity\EventPlugin;
use eCamp\Lib\Service\ServiceUtils;

abstract class PluginStrategyBase implements PluginStrategyInterface {
    /** @var ServiceUtils */
    private $serviceUtils;

    public function __construct(
        ServiceUtils $serviceUtils
    ) {
        $this->serviceUtils = $serviceUtils;
    }

    abstract public function eventPluginExtract(EventPlugin $eventPlugin): array;

    abstract public function eventPluginCreated(EventPlugin $eventPlugin): void;

    /**
     * @return ServiceUtils
     */
    protected function getServiceUtils() {
        return $this->serviceUtils;
    }
}
