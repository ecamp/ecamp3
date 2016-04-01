<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\Camp;
use EcampCore\Entity\User;
use EcampCore\Entity\CampCollaboration;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

/**
 * Class CampCollaborationRepository
 * @package EcampCore\Repository
 *
 * @method CampCollaboration find($id)
 */
class CampCollaborationRepository extends EntityRepository
{

    public function getCollection(array $criteria)
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

        if (isset($criteria["status"]) && !is_null($criteria["status"])) {
            $q->andWhere('cc.status = :status');
            $q->setParameter('status', $criteria["status"]);
        }

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
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
