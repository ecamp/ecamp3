<?php

namespace EcampCore\RepositoryTraits;

trait EventCategoryRepositoryTrait
{
    /**
     * @var Doctrine\ORM\EntityRepository
     */
    private $eventCategoryRepository;

    /**
     * @return Doctrine\ORM\EntityRepository
     */
    public function getEventCategoryRepository()
    {
        return $this->eventCategoryRepository;
    }

    public function setEventCategoryRepository($eventCategoryRepository)
    {
        $this->eventCategoryRepository = $eventCategoryRepository;

        return $this;
    }
}
