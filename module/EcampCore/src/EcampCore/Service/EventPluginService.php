<?php

namespace EcampCore\Service;

use EcampCore\Entity\EventPlugin;
use EcampCore\Repository\EventPluginRepository;

class EventPluginService extends Base\ServiceBase
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
