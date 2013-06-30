<?php

namespace EcampCore\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

class CampCollaborationHelper
{

    /**
     * @var ArrayCollection
     */
    private $collaborations;

    public function __construct($collaborations)
    {
        $this->collaborations = $collaborations;
    }

    /****************************************************************
     * CampCollaboration:
     *
     * - getUsers()
     * - getCollaboration(User $user)
     * - getCollaborations($role = null)
     * - getReceivedCollaborationRequests($role = null)
     * - getSentCollaborationInvitations($role = null)
     *
     * - hasCollaborator(User $user, $role = null)
     *
     * - hasReceivedCollaborationRequestFrom(User $user)
     * - hasSentCollaborationInvitationTo(User $user)
     *
     * - canSendCollaborationInvitation(User $user)
     *
     ****************************************************************/

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_ESTABLISHED));

        return $this->collaborations
            ->matching($criteria)
            ->map(function($cc){ return $cc->getUser(); });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGuest()
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_ESTABLISHED));
        $criteria->andWhere($expr->eq('role', CampCollaboration::ROLE_GUEST));

        return $this->collaborations
            ->matching($criteria)
            ->map(function($cc){ return $cc->getUser(); });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_ESTABLISHED));
        $criteria->andWhere($expr->eq('role', CampCollaboration::ROLE_MEMBER));

        return $this->collaborations
            ->matching($criteria)
            ->map(function($cc){ return $cc->getUser(); });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getManagers()
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_ESTABLISHED));
        $criteria->andWhere($expr->eq('role', CampCollaboration::ROLE_MANAGER));

        return $this->collaborations
            ->matching($criteria)
            ->map(function($cc){ return $cc->getUser(); });
    }

    /**
     * @param  User              $user
     * @return CampCollaboration
     */
    public function getCollaboration(User $user)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_ESTABLISHED));
        $criteria->andWhere($expr->eq('user', $user));
        $criteria->setMaxResults(1);

        $list = $this->collaborations->matching($criteria);

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
    public function getCollaborations($role = null)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_ESTABLISHED));

        if (isset($role)) {
            $criteria->andWhere($expr->eq('role', $role));
        }

        return $this->collaborations->matching($criteria);
    }

    /**
     * @param  string                                  $role
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReceivedCollaborationRequests($role = null)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_REQUESTED));

        if (isset($role)) {
            $criteria->andWhere($expr->eq('role', $role));
        }

        return $this->collaborations->matching($criteria);
    }

    /**
     * @param  string                                  $role
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSentCollaborationInvitations($role = null)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_INVITED));

        if (isset($role)) {
            $criteria->andWhere($expr->eq('role', $role));
        }

        return $this->collaborations->matching($criteria);
    }

    /**
     * @param  User    $user
     * @param  string  $role
     * @return boolean
     */
    public function hasCollaborator(User $user, $role = null)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_ESTABLISHED));
        $criteria->andWhere($expr->eq('user', $user));
        $criteria->setMaxResults(1);

        if (isset($role)) {
            $criteria->andWhere($expr->eq('role', $role));
        }

        return ! $this->collaborations->matching($criteria)->isEmpty();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function hasReceivedCollaborationRequestFrom(User $user)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_REQUESTED));
        $criteria->andWhere($expr->eq('user', $user));
        $criteria->setMaxResults(1);

        return ! $this->collaborations->matching($criteria)->isEmpty();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function hasSentCollaborationInvitationTo(User $user)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_INVITED));
        $criteria->andWhere($expr->eq('user', $user));
        $criteria->setMaxResults(1);

        return ! $this->collaborations->matching($criteria)->isEmpty();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function canSendCollaborationInvitation(User $user)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->andWhere($expr->eq('user', $user));
        $criteria->setMaxResults(1);

        return $this->collaborations->matching($criteria)->isEmpty();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function isGuest(User $user)
    {
        $collaboration = $this->getCollaboration($user);

        return $collaboration != null && $collaboration->isGuest();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function isMember(User $user)
    {
        $collaboration = $this->getCollaboration($user);

        return $collaboration != null && $collaboration->isMember();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function isManager(User $user)
    {
        $collaboration = $this->getCollaboration($user);

        return $collaboration != null && $collaboration->isManager();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function isOwner(User $user)
    {
        $collaboration = $this->getCollaboration($user);

        return $collaboration != null && $collaboration->isOwner();
    }

}
