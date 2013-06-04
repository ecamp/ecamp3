<?php

namespace EcampCore\RepositoryTraits;

trait EventInstanceRepositoryTrait
{
    /**
     * @var EcampCore\Repository\EventInstanceRepository
     */
    private $eventInstanceRepository;

    /**
     * @return EcampCore\Repository\EventInstanceRepository
     */
    public function getEventInstanceRepository()
    {
        return $this->eventInstanceRepository;
    }

    public function setEventInstanceRepository($eventInstanceRepository)
    {
        $this->eventInstanceRepository = $eventInstanceRepository;

        return $this;
    }
}
