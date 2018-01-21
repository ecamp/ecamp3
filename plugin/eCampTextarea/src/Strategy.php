<?php

namespace eCamp\Plugin\Textarea;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Plugin\PluginStrategyInterface;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Plugin\Textarea\Entity\Textarea;
use eCamp\Plugin\Textarea\Service\TextareaService;
use ZF\Hal\Link\Link;

class Strategy implements PluginStrategyInterface
{
    /** @var TextareaService */
    private $textareaService;

    public function __construct(TextareaService $textareaService) {
        $this->textareaService = $textareaService;
    }


    function getHalLinks(EventPlugin $eventPlugin) : array {
        return [
            'textarea' => Link::factory([
                'rel' => 'textarea',
                'route' => [
                    'name' => 'ecamp.api.event_plugin/ecamp.textarea',
                    'params' => [ 'event_plugin_id' => $eventPlugin->getId() ]
                ]
            ])

        ];
    }

    /**
     * @param EventPlugin $eventPlugin
     * @throws ORMException
     * @throws NoAccessException
     */
    function postCreated(EventPlugin $eventPlugin): void {
        /** @var Textarea $textarea */
        $textarea = $this->textareaService->create((object)[]);
        $textarea->setEventPlugin($eventPlugin);
    }

}
