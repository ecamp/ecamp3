<?php

namespace eCamp\Plugin\Storyboard;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Plugin\PluginStrategyBase;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Plugin\Storyboard\Service\SectionService;
use ZF\Hal\Link\Link;

class Strategy extends PluginStrategyBase {
    /** @var SectionService */
    private $sectionService;

    public function __construct(SectionService $sectionService) {
        $this->sectionService = $sectionService;
    }

    public function eventPluginExtract(EventPlugin $eventPlugin): array {
        return [
            'section' => Link::factory([
                'rel' => 'section',
                'route' => [
                    'name' => 'ecamp.api.event_plugin/ecamp.section',
                    'params' => [
                        'event_plugin_id' => $eventPlugin->getId(),
                        'section_id' => null,
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
        $this->sectionService->create((object) [
            'event_plugin_id' => $eventPlugin->getId(),
            'pos' => 0,
        ]);
    }
}
