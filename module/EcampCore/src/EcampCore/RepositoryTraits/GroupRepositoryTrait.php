<?php

namespace EcampCore\RepositoryTraits;

trait GroupRepositoryTrait
{
    /**
     * @var EcampCore\Repository\GroupRepository
     */
    private $groupRepository;

    /**
     * @return EcampCore\Repository\GroupRepository
     */
    public function getGroupRepository()
    {
        return $this->groupRepository;
    }

    public function setGroupRepository($groupRepository)
    {
        $this->groupRepository = $groupRepository;

        return $this;
    }
}
