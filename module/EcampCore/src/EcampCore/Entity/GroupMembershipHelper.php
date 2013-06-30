<?php

namespace EcampCore\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

class GroupMembershipHelper
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
     * - getMembers()
     * - getMembership(User $user)
     * - getMemberships($role = null)
     * - getReceivedMembershipRequests($role = null)
     * - getSentMembershipInvitations($role = null)
     *
     * - hasMember(User $user, $role = null)
     *
     * - hasReceivedMembershipRequestFrom(User $user)
     * - hasSentMembershipInvitationTo(User $user)
     *
     * - canSendMembershipInvitation(User $user)
     *
     ****************************************************************/

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_ESTABLISHED));

        return $this->memberships
            ->matching($criteria)
            ->map(function($gm){ return $gm->getUser(); });
    }

    /**
     * @param  User            $user
     * @return GroupMembership
     */
    public function getMembership(User $user)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_ESTABLISHED));
        $criteria->andWhere($expr->eq('user', $user));
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
    public function getReceivedMembershipRequests($role = null)
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
    public function getSentMembershipInvitations($role = null)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_INVITED));

        if (isset($role)) {
            $criteria->andWhere($expr->eq('role', $role));
        }

        return $this->memberships->matching($criteria);
    }

    /**
     * @param  User    $user
     * @param  string  $role
     * @return boolean
     */
    public function hasMember(User $user, $role = null)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_ESTABLISHED));
        $criteria->andWhere($expr->eq('user', $user));
        $criteria->setMaxResults(1);

        if (isset($role)) {
            $criteria->andWhere($expr->eq('role', $role));
        }

        return !$this->memberships->matching($criteria)->isEmpty();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function hasReceivedMembershipRequestFrom(User $user)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_REQUESTED));
        $criteria->andWhere($expr->eq('user', $user));
        $criteria->setMaxResults(1);

        return !$this->memberships->matching($criteria)->isEmpty();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function hasSentMembershipInvitationTo(User $user)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', GroupMembership::STATUS_INVITED));
        $criteria->andWhere($expr->eq('user', $user));
        $criteria->setMaxResults(1);

        return !$this->memberships->matching($criteria)->isEmpty();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function canSendMembershipInvitation(User $user)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->andWhere($expr->eq('user', $user));
        $criteria->setMaxResults(1);

        return $this->memberships->matching($criteria)->isEmpty();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function isManager(User $user)
    {
        $membership = $this->getMembership($user);

        return $membership != null && $membership->isManager();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function isMember(User $user)
    {
        $membership = $this->getMembership($user);

        return $membership != null && $membership->isMember();
    }

}
