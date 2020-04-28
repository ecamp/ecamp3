<?php

namespace eCamp\Core\Plugin;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\MappedSuperclass
 */
abstract class BasePluginEntity extends BaseEntity {
    /**
     * @var EventPlugin
     * @ORM\ManyToOne(targetEntity="eCamp\Core\Entity\EventPlugin")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $eventPlugin;

    /**
     * @return EventPlugin
     */
    public function getEventPlugin() {
        return $this->eventPlugin;
    }

    public function setEventPlugin(EventPlugin $eventPlugin) {
        $this->eventPlugin = $eventPlugin;
    }
}
