<?php

namespace eCamp\Plugin\Storyboard;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Plugin\PluginStrategyBase;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use eCamp\Plugin\Storyboard\Service\SectionService;
use ZF\Hal\Link\Link;

class Strategy extends PluginStrategyBase {
    /** @var SectionService */
    private $sectionService;

    public function __construct(SectionService $sectionService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->sectionService = $sectionService;
    }

    public function eventPluginExtract(EventPlugin $eventPlugin): array {
        return [
            'sections' => Link::factory([
                'rel' => 'sections',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event-plugin.storyboard',
                    'options' => ['query' => ['event_plugin_id' => $eventPlugin->getId()]],
                ],
            ]),
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function eventPluginCreated(EventPlugin $eventPlugin): void {
        $section = $this->sectionService->createEntity(['pos' => 0], $eventPlugin);
        $this->getServiceUtils()->emPersist($section);
    }
}
