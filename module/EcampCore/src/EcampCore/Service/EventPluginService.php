<?php

namespace EcampCore\Service;

use EcampLib\Service\ServiceBase;
use EcampCore\Entity\EventPlugin;
use EcampCore\Repository\EventPluginRepository;

class EventPluginService
    extends ServiceBase
{
    /**
     * @var \EcampCore\Repository\EventPluginRepository
     */
    private $eventPluginRepository;

    public function __construct(
            EventPluginRepository $eventPluginRepository
    ){
        $this->eventPluginRepository = $eventPluginRepository;
    }

    public function deleteEventPlugin(EventPlugin $eventPlugin)
    {
        $this->remove($eventPlugin);
    }
}
