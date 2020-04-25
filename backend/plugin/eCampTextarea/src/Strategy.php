<?php

namespace eCamp\Plugin\Textarea;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Plugin\PluginStrategyBase;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Plugin\Textarea\Service\TextareaService;
use ZF\Hal\Link\Link;

class Strategy extends PluginStrategyBase {
    /** @var TextareaService */
    private $textareaService;

    public function __construct(TextareaService $textareaService) {
        $this->textareaService = $textareaService;
    }

    /**
     * @return array
     */
    public function eventPluginExtract(EventPlugin $eventPlugin): array {
        return [
            'textarea' => Link::factory([
                'rel' => 'textarea',
                'route' => [
                    'name' => 'ecamp.api.event_plugin/ecamp.textarea',
                    'params' => [
                        'event_plugin_id' => $eventPlugin->getId(),
                        'textarea_id' => null,
                    ],
                ],
            ]),
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function eventPluginCreated(EventPlugin $eventPlugin): void {
        $this->textareaService->create((object) [
            'event_plugin_id' => $eventPlugin->getId(),
        ]);
    }
}
