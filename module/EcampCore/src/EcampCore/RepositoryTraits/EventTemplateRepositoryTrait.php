<?php

namespace EcampCore\RepositoryTraits;

trait EventTemplateRepositoryTrait
{
    /**
     * @var Doctrine\ORM\EntityRepository
     */
    private $eventTemplateRepository;

    /**
     * @return Doctrine\ORM\EntityRepository
     */
    public function getEventTemplateRepository()
    {
        return $this->eventTemplateRepository;
    }

    public function setEventTemplateRepository($eventTemplateRepository)
    {
        $this->eventTemplateRepository = $eventTemplateRepository;

        return $this;
    }
}
