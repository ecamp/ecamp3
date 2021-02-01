<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\GroupMembershipHydrator;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class GroupMembershipService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            GroupMembership::class,
            GroupMembershipHydrator::class,
            $authenticationService
        );
    }

    /**
     * @param mixed $data
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    protected function createEntity($data): GroupMembership {
        $authUser = $this->getAuthUser();
        if (!isset($data->userId)) {
            $data->userId = $authUser->getId();
        }

        /** @var Group $group */
        $group = $this->findEntity(Group::class, $data->groupId);
        /** @var User $user */
        $user = $this->findEntity(User::class, $data->userId);

        if (!isset($data->role)) {
            $data->role = GroupMembership::ROLE_MEMBER;
        }

        /** @var GroupMembership $groupMembership */
        $groupMembership = parent::createEntity($data);
        $groupMembership->setGroup($group);
        $groupMembership->setUser($user);
        $groupMembership->setRole($data->role);

        if ($data->userId === $authUser->getId()) {
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
     * @param mixed $data
     *
     * @throws ORMException
     * @throws \Exception
     */
    protected function updateEntity(BaseEntity $entity, $data): GroupMembership {
        /** @var GroupMembership $groupMembership */
        $groupMembership = parent::updateEntity($entity, $data);

        if ($groupMembership->isEstablished()) {
            $this->updateMembership($groupMembership, $data);
        } elseif ($groupMembership->isInvitation()) {
            $this->updateInvitation($groupMembership, $data);
        } elseif ($groupMembership->isRequest()) {
            $this->updateRequest($groupMembership, $data);
        }

        return $groupMembership;
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['group'])) {
            $q->andWhere('row.group = :group');
            $q->setParameter('group', $params['group']);
        }
        if (isset($params['user'])) {
            $q->andWhere('row.user = :user');
            $q->setParameter('user', $params['user']);
        }

        return $q;
    }

    /**
     * @param $data
     *
     * @throws \Exception
     */
    private function updateMembership(GroupMembership $groupMembership, $data) {
        // TODO: ACL-Check can update Membership

        if (isset($data->role)) {
            $groupMembership->setRole($data->role);
        }

        if (isset($data->status) && GroupMembership::STATUS_UNRELATED == $data->status) {
            $this->delete($groupMembership->getId());
        }
    }

    /**
     * @param $data
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    private function updateInvitation(GroupMembership $groupMembership, $data) {
        $authUser = $this->getAuthUser();

        // TODO: ACL-Check can update Invitation

        if ($authUser === $groupMembership->getUser()) {
            if (GroupMembership::STATUS_UNRELATED == $data->status) {
                $this->delete($groupMembership->getId());
            }
            if (GroupMembership::STATUS_ESTABLISHED == $data->status) {
                $groupMembership->setStatus(GroupMembership::STATUS_ESTABLISHED);
            }
        } else {
            if (GroupMembership::STATUS_UNRELATED == $data->status) {
                $this->delete($groupMembership->getId());
            }
            if (isset($data->role)) {
                $groupMembership->setRole($data->role);
            }
        }
    }

    /**
     * @param $data
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    private function updateRequest(GroupMembership $groupMembership, $data) {
        $authUser = $this->getAuthUser();

        // TODO: ACL-Check can update Request

        if ($authUser === $groupMembership->getUser()) {
            if (isset($data->role)) {
                $groupMembership->setRole($data->role);
            }
            if (GroupMembership::STATUS_UNRELATED == $data->status) {
                $this->delete($groupMembership->getId());
            }
        } else {
            if (GroupMembership::STATUS_UNRELATED == $data->status) {
                $this->delete($groupMembership->getId());
            }
            if (GroupMembership::STATUS_ESTABLISHED == $data->status) {
                if (isset($data->role)) {
                    $groupMembership->setRole($data->role);
                }
                $groupMembership->setMembershipAcceptedBy($authUser->getUsername());
                $groupMembership->setStatus(GroupMembership::STATUS_ESTABLISHED);
            }
        }
    }
}
