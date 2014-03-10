<?php

namespace EcampStoryboard;

use EcampCore\Entity\Medium;
use EcampCore\Entity\EventPlugin;
use EcampCore\Plugin\AbstractStrategyFactory;

class StrategyFactory
    extends AbstractStrategyFactory
{
    public function createStrategy(EventPlugin $eventPlugin, Medium $medium)
    {
        return new Strategy($this->serviceLocator, $this->entityManager, $eventPlugin, $medium);
    }
}
