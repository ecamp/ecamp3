<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use eCamp\Core\Hydrator\GroupMembershipHydrator;
use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;
use ZF\ApiProblem\ApiProblem;

class GroupMembershipService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , GroupMembershipHydrator $groupMembershipHydrator
    ) {
        parent::__construct($acl, $entityManager, $groupMembershipHydrator, GroupMembership::class);
    }

    /**
     * @param mixed $data
     * @return GroupMembership|ApiProblem
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function create($data) {
        $authUser = $this->getAuthUser();
        if (!isset($data->user_id)) {
            $data->user_id = $authUser->getId();
        }

        /** @var Group $group */
        $group = $this->findEntity(Group::class, $data->group_id);
        /** @var User $user */
        $user = $this->findEntity(User::class, $data->user_id);

        if (!isset($data->role)) {
            $data->role = GroupMembership::ROLE_MEMBER;
        }

        /** @var GroupMembership $groupMembership */
        $groupMembership = parent::create($data);
        $groupMembership->setGroup($group);
        $groupMembership->setUser($user);
        $groupMembership->setRole($data->role);

        if ($data->user_id === $authUser->getId()) {
            // Create GroupMembership for AuthUser
            $groupMembership->setStatus(GroupMembership::STATUS_REQUESTED);

        } else {
            $groupMembership->setStatus($groupMembership::STATUS_INVITED);
            $groupMembership->setMembershipAcceptedBy($authUser->getUsername());
        }

        $group->addGroupMembership($groupMembership);
        $user->addGroupMembership($groupMembership);

        return $groupMembership;
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return GroupMembership|ApiProblem
     * @throws ORMException
     * @throws \Exception
     */
    public function update($id, $data) {

        /** @var GroupMembership $groupMembership */
        $groupMembership = parent::update($id, $data);

        if ($groupMembership->isEstablished()) {
            $this->updateMembership($groupMembership, $data);
        } elseif ($groupMembership->isInvitation()) {
            $this->updateInvitation($groupMembership, $data);
        } elseif ($groupMembership->isRequest()) {
            $this->updateRequest($groupMembership, $data);
        }

        return $groupMembership;
    }

    /**
     * @param GroupMembership $groupMembership
     * @param $data
     * @throws \Exception
     */
    private function updateMembership(GroupMembership $groupMembership, $data) {
        // TODO: ACL-Check can update Membership

        if (isset($data->role)) { $groupMembership->setRole($data->role); }

        if (isset($data->status) && $data->status == GroupMembership::STATUS_UNRELATED) {
            $this->delete($groupMembership->getId());
        }
    }

    /**
     * @param GroupMembership $groupMembership
     * @param $data
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    private function updateInvitation(GroupMembership $groupMembership, $data) {
        $authUser = $this->getAuthUser();

        // TODO: ACL-Check can update Invitation

        if ($authUser === $groupMembership->getUser()) {
            if ($data->status == GroupMembership::STATUS_UNRELATED) {
                $this->delete($groupMembership->getId());
            }
            if ($data->status == GroupMembership::STATUS_ESTABLISHED) {
                $groupMembership->setStatus(GroupMembership::STATUS_ESTABLISHED);
            }
        } else {
            if ($data->status == GroupMembership::STATUS_UNRELATED) {
                $this->delete($groupMembership->getId());
            }
            if(isset($data->role)) { $groupMembership->setRole($data->role); }
        }
    }

    /**
     * @param GroupMembership $groupMembership
     * @param $data
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    private function updateRequest(GroupMembership $groupMembership, $data) {
        $authUser = $this->getAuthUser();

        // TODO: ACL-Check can update Request

        if ($authUser === $groupMembership->getUser()) {
            if(isset($data->role)) { $groupMembership->setRole($data->role); }
            if ($data->status == GroupMembership::STATUS_UNRELATED) {
                $this->delete($groupMembership->getId());
            }
        } else {
            if ($data->status == GroupMembership::STATUS_UNRELATED) {
                $this->delete($groupMembership->getId());
            }
            if ($data->status == GroupMembership::STATUS_ESTABLISHED) {
                if(isset($data->role)) { $groupMembership->setRole($data->role); }
                $groupMembership->setMembershipAcceptedBy($authUser->getUsername());
                $groupMembership->setStatus(GroupMembership::STATUS_ESTABLISHED);
            }
        }
    }


}
