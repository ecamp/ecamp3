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

    public function eventPluginExtract(EventPlugin $eventPlugin): array {
        $textarea = $this->textareaService->fetchFromEventPlugin($eventPlugin->getId());

        if (!$textarea) {
            return [];
        }

        return [
            'textarea' => Link::factory([
                'rel' => 'textarea',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event-plugin.textarea',
                    'params' => [
                        'event_plugin_id' => $eventPlugin->getId(),
                        'textarea_id' => $textarea->getId(),
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
        $this->textareaService->create([], true, $eventPlugin);
    }
}
