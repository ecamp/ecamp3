<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

class ContributorRepository extends EntityRepository
{
    /**
     * @var CoreApi\Acl\ContextProvider
     * @Inject CoreApi\Acl\ContextProvider
     */
    protected $contextProvider;

    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
    }

    public function findCollaboratorsByCamp($campId)
    {
        $query = $this->createQueryBuilder("uc")
                    ->where("uc.camp = '$campId'")
                    ->andwhere("uc.invitationAccepted > 0")
                    ->andwhere("uc.requestAcceptedBy is not null")
                    ->getQuery();

        return $query->getResult();
    }

    public function findCollaboratorsByUser(\EcampCore\Entity\User $user)
    {
        $query = $this->createQueryBuilder("uc")
                    ->where("uc.user = '" . $user->getId() . "'")
                    ->andwhere("uc.invitationAccepted > 0")
                    ->andwhere("uc.requestAcceptedBy is not null")
                    ->getQuery();

        return $query->getResult();
    }

    public function findOpenRequestsByCamp($campId)
    {
        $query = $this->createQueryBuilder("uc")
                    ->where("uc.camp = '$campId'")
                    ->andwhere("uc.invitationAccepted > 0")
                    ->andwhere("uc.requestAcceptedBy is null")
                    ->getQuery();

        return $query->getResult();
    }

    public function findOpenInvitationsByUser(\EcampCore\Entity\User $user)
    {
        $query = $this->createQueryBuilder("uc")
                    ->where("uc.user = '" . $user->getId() . "'")
                    ->andwhere("uc.invitationAccepted = 0")
                    ->andwhere("uc/requestAcceptedBy is not null")
                    ->getQuery();

        return $query->getResult();
    }

}
