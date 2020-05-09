<?php

namespace eCamp\Plugin\Textarea;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Plugin\PluginStrategyBase;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use eCamp\Plugin\Textarea\Service\TextareaService;
use ZF\Hal\Link\Link;

class Strategy extends PluginStrategyBase {
    /** @var TextareaService */
    private $textareaService;

    public function __construct(TextareaService $textareaService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->textareaService = $textareaService;
    }

    public function eventPluginExtract(EventPlugin $eventPlugin): array {
        $textarea = $this->textareaService->findOneByEventPlugin($eventPlugin->getId());

        if (!$textarea) {
            return [];
        }

        return [
            'textarea' => $textarea,

            /*
            // Alternatively send link only
            'textarea' => Link::factory([
                'rel' => 'textarea',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event-plugin.textarea',
                    'params' => [
                        'eventPluginId' => $eventPlugin->getId(),
                        'textareaId' => $textarea->getId(),
                    ],
                ],
            ]),*/
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function eventPluginCreated(EventPlugin $eventPlugin): void {
        $textarea = $this->textareaService->createEntity([], $eventPlugin);
        $this->getServiceUtils()->emPersist($textarea);
    }
}
