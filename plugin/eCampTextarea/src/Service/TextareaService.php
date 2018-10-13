<?php

namespace eCamp\Plugin\Textarea\Service;

use eCamp\Core\Plugin\BasePluginService;
use eCamp\Plugin\Textarea\Entity\Textarea;
use eCamp\Plugin\Textarea\Hydrator\TextareaHydrator;

class TextareaService extends BasePluginService {
    public function __construct($eventPluginId) {
        parent::__construct(
            Textarea::class,
            TextareaHydrator::class,
            $eventPluginId
        );
    }
}
