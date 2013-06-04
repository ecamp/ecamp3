<?php

namespace EcampCore\RepositoryTraits;

trait UserRelationshipRepositoryTrait
{
    /**
     * @var EcampCore\Repository\UserRelationshipRepository
     */
    private $userRelationshipRepository;

    /**
     * @return EcampCore\Repository\UserRelationshipRepository
     */
    public function getUserRelationshipRepository()
    {
        return $this->userRelationshipRepository;
    }

    public function setUserRelationshipRepository($userRelationshipRepository)
    {
        $this->userRelationshipRepository = $userRelationshipRepository;

        return $this;
    }
}
