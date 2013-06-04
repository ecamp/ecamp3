<?php

namespace EcampCore\RepositoryTraits;

trait EventRespRepositoryTrait
{
    /**
     * @var EcampCore\Repository\EventRespRepository
     */
    private $eventRespRepository;

    /**
     * @return EcampCore\Repository\EventRespRepository
     */
    public function getEventRespRepository()
    {
        return $this->eventRespRepository;
    }

    public function setEventRespRepository($eventRespRepository)
    {
        $this->eventRespRepository = $eventRespRepository;

        return $this;
    }
}
