<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\Camp;
use EcampCore\Entity\User;
use EcampCore\Entity\CampCollaboration;

class CampCollaborationRepository extends EntityRepository
{

    public function findForApi(array $criteria)
    {
        $q = $this->createQueryBuilder('cc');

        if (isset($criteria["camp"]) && !is_null($criteria["camp"])) {
            $q->andWhere('cc.camp = :camp');
            $q->setParameter('camp', $criteria["camp"]);
        }

        if (isset($criteria["user"]) && !is_null($criteria["user"])) {
            $q->andWhere('cc.user = :user');
            $q->setParameter('user', $criteria["user"]);
        }

        if (isset($criteria["offset"]) && !is_null($criteria["offset"])) {
            $q->setFirstResult($criteria["offset"]);
            $q->setMaxResults(100);
        }
        if (isset($criteria["limit"]) && !is_null($criteria["limit"])) {
            $q->setMaxResults($criteria["limit"]);
        }

        return $q->getQuery()->getResult();
    }

    /**
     * @param  Camp              $camp
     * @param  User              $user
     * @return CampCollaboration
     */
    public function findByCampAndUser(Camp $camp, User $user)
    {
        $query = $this->createQueryBuilder("cc")
                    ->where("cc.camp = '" . $camp->getId() . "'")
                    ->andWhere("cc.user = '" . $user->getId() . "'")
                    ->setMaxResults(1)
                    ->getQuery();

           $cc = $query->getResult();

           return (count($cc) > 0) ? $cc[0] : null;
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
