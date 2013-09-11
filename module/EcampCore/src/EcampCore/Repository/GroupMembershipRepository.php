<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\GroupMembership;

class GroupMembershipRepository extends EntityRepository
{

    /**
     * @param  Group           $group
     * @param  User            $user
     * @return GroupMembership
     */
    public function findByGroupAndUser(Group $group, User $user)
    {
        $query = $this->createQueryBuilder('gm')
                    ->where("gm.group = '" . $group->getId() . "'")
                    ->andWhere("gm.user = '" . $user->getId() . "'")
                    ->setMaxResults(1)
                    ->getQuery();

        $gm = $query->getResult();

        return (count($gm) > 0) ? $gm[0] : null;
    }

}
