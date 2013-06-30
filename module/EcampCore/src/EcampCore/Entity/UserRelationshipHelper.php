<?php

namespace EcampCore\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

class UserRelationshipHelper
{
    /**
     * @var ArrayCollection
     */
    private $relationshipTo;

    /**
     * @var ArrayCollection
     */
    private $relationshipFrom;

    /**
     * @param ArrayCollection $relTo
     * @param ArrayCollection $relFrom
     */
    public function __construct($relationshipTo, $relationshipFrom)
    {
        $this->relationshipTo = $relationshipTo;
        $this->relationshipFrom = $relationshipFrom;
    }

    /****************************************************************
     * User Relationship:
     *
     * - getFriends
     * - getSentFriendshipRequests
     * - getReceivedFriendshipRequests
     *
     * - isFriend
     * - hasSentFriendshipRequestTo
     * - hasReceivedFriendshipRequestFrom
     *
     * - canSendFriendshipRequest
     *
     ****************************************************************/

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFriends()
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('type', UserRelationship::TYPE_FRIEND));
        $criteria->andWhere($expr->neq('counterpart', null));

        return $this->relationshipTo
            ->matching($criteria)
            ->map(function($ur){ return $ur->getTo(); });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSentFriendshipRequests()
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('type', UserRelationship::TYPE_FRIEND));
        $criteria->andWhere($expr->isNull('counterpart'));

        return $this->relationshipTo
            ->matching($criteria)
            ->map(function($ur){ return $ur->getTo(); });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReceivedFriendshipRequests()
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('type', UserRelationship::TYPE_FRIEND));
        $criteria->andWhere($expr->isNull('counterpart'));

        return $this->relationshipFrom
            ->matching($criteria)
            ->map(function($ur){ return $ur->getFrom(); });
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function isFriend(User $user)
    {
        $criteriaFrom = Criteria::create();
        $criteriaTo   = Criteria::create();
        $expr = Criteria::expr();

        $criteriaFrom->where($expr->eq('type', UserRelationship::TYPE_FRIEND));
        $criteriaFrom->andWhere($expr->neq('counterpart', null));
        $criteriaFrom->andWhere($expr->eq('to', $user));
        $criteriaFrom->setMaxResults(1);

        return !$this->relationshipTo->matching($criteriaFrom)->isEmpty();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function hasSentFriendshipRequestTo(User $user)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('type', UserRelationship::TYPE_FRIEND));
        $criteria->andWhere($expr->eq('to', $user));
        $criteria->andWhere($expr->isNull('counterpart'));
        $criteria->setMaxResults(1);

        return !$this->relationshipTo->matching($criteria)->isEmpty();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function hasReceivedFriendshipRequestFrom(User $user)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('type', UserRelationship::TYPE_FRIEND));
        $criteria->andWhere($expr->eq('from', $user));
        $criteria->andWhere($expr->isNull('counterpart'));
        $criteria->setMaxResults(1);

        return !$this->relationshipFrom->matching($criteria)->isEmpty();
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function canSendFriendshipRequest(User $user)
    {
        $criteriaFrom = Criteria::create();
        $criteriaTo   = Criteria::create();
        $expr = Criteria::expr();

        $criteriaFrom->where($expr->eq('to', $user));
        $criteriaFrom->setMaxResults(1);

        $criteriaTo->where($expr->eq('from', $user));
        $criteriaTo->setMaxResults(1);

        return
            $this->relationshipTo->matching($criteriaFrom)->isEmpty() &&
            $this->relationshipFrom->matching($criteriaTo)->isEmpty();
    }

}
