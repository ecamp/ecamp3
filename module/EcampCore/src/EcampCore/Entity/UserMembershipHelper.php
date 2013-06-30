<?php

namespace EcampCore\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

class UserMembershipHelper
{
    /**
     * @var ArrayCollection
     */
    private $memberships;

    public function __construct($memberships)
    {
        $this->memberships = $memberships;
    }

    /****************************************************************
     * GroupMembership:
     *
     * - getGroups()
     * - getMembership(Group $group)
     * - getMemberships($role = null)
     * - getSentMembershipRequests($role = null)
     * - getReceivedMembershipInvitations($role = null)
     *
     * - isMemberOf($group, $role = null)
     *
     * - hasSentMembershipRequestTo(Group $group)
     * - hasReceivedMembershipInvitationFrom(Group $group)
     *
     * - canSendMembershipRequest
     *
     ****************************************************************/

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_ESTABLISHED));

        return $this->memberships
            ->matching($criteria)
            ->map(function($ug){ return $ug->getGroup(); });
    }

    /**
     * @param  Group           $group
     * @return GroupMembership
     */
    public function getMembership(Group $group)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_ESTABLISHED));
        $criteria->andWhere($expr->eq('group', $group));
        $criteria->setMaxResults(1);

        $list = $this->memberships->matching($criteria);

        if ($list->count() == 1) {
            return $list->first();
        } else {
            return null;
        }
    }

    /**
     * @param  string                                  $role
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMemberships($role = null)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_ESTABLISHED));

        if (isset($role)) {
            $criteria->andWhere($expr->eq('role', $role));
        }

        return $this->memberships->matching($criteria);
    }

    /**
     * @param  string                                  $role
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSentMembershipRequests($role = null)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_REQUESTED));

        if (isset($role)) {
            $criteria->andWhere($expr->eq('role', $role));
        }

        return $this->memberships->matching($criteria);
    }

    /**
     * @param  string                                  $role
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReceivedMembershipInvitations($role = null)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_INVITED));

        if (!is_null($role)) {
            $criteria->andWhere($expr->eq('role', $role));
        }

        return $this->memberships->matching($criteria);
    }

    /**
     * @param  Group   $group
     * @param  string  $role
     * @return boolean
     */
    public function isMemberOf(Group $group, $role = null)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_ESTABLISHED));
        $criteria->andWhere($expr->eq('group', $group));
        $criteria->setMaxResults(1);

        if (isset($role)) {
            $criteria->andWhere($expr->eq('role', $role));
        }

        return !$this->memberships->matching($criteria)->isEmpty();
    }

    /**
     * @param  Group   $group
     * @return boolean
     */
    public function hasSentMembershipRequestTo(Group $group)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_REQUESTED));
        $criteria->andWhere($expr->eq('group', $group));
        $criteria->setMaxResults(1);

        return !$this->memberships->matching($criteria)->isEmpty();
    }

    /**
     * @param  Group   $group
     * @return boolean
     */
    public function hasReceivedMembershipInvitationFrom(Group $group)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_INVITED));
        $criteria->andWhere($expr->eq('group', $group));
        $criteria->setMaxResults(1);

        return !$this->memberships->matching($criteria)->isEmpty();
    }

    /**
     * @param  Group   $group
     * @return boolean
     */
    public function canSendMembershipRequest(Group $group)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->andWhere($expr->eq('group', $group));
        $criteria->setMaxResults(1);

        return $this->memberships->matching($criteria)->isEmpty();
    }

}
