<?php

namespace EcampCore\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

class UserCollaborationHelper
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
     * - getCamps()
     * - getCollaboration(Camp $camp)
     * - getCollaborations($role = null)
     * - getSentCollaborationRequests($role = null)
     * - getReceivedCollaborationInvitations($role = null)
     *
     * - isCollaboratorOf(Camp $camp, $role = null)
     *
     * - hasSentCollaborationRequestTo(Camp $camp)
     * - hasReceivedCollaborationInvitationFrom(Camp $camp)
     *
     * - canSendCollaborationRequest(Camp $camp)
     *
     ****************************************************************/

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCamps()
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_ESTABLISHED));

        return $this->collaborations
            ->matching($criteria)
            ->map(function($cc){ return $cc->getCamp(); });
    }

    /**
     * @param  Camp              $camp
     * @return CampCollaboration
     */
    public function getCollaboration(Camp $camp)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        //$criteria->where($expr->eq('status', CampCollaboration::STATUS_ESTABLISHED));
        $criteria->andWhere($expr->eq('camp', $camp));
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
    public function getSentCollaborationRequests($role = null)
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
    public function getReceivedCollaborationInvitations($role = null)
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
     * @param  Camp    $camp
     * @param  string  $role
     * @return boolean
     */
    public function isCollaboratorOf(Camp $camp, $role = null)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_ESTABLISHED));
        $criteria->andWhere($expr->eq('camp', $camp));
        $criteria->setMaxResults(1);

        if (isset($role)) {
            $criteria->andWhere($expr->eq('role', $role));
        }

        return !$this->collaborations->matching($criteria)->isEmpty();
    }

    /**
     * @param  Camp    $camp
     * @return boolean
     */
    public function hasSentCollaborationRequestTo(Camp $camp)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_REQUESTED));
        $criteria->andWhere($expr->eq('camp', $camp));
        $criteria->setMaxResults(1);

        return !$this->collaborations->matching($criteria)->isEmpty();
    }

    /**
     * @param  Camp    $camp
     * @return boolean
     */
    public function hasReceivedCollaborationInvitationFrom(Camp $camp)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('status', CampCollaboration::STATUS_INVITED));
        $criteria->andWhere($expr->eq('camp', $camp));
        $criteria->setMaxResults(1);

        return !$this->collaborations->matching($criteria)->isEmpty();
    }

    /**
     * @param  Camp    $camp
     * @return boolean
     */
    public function canSendCollaborationRequest(Camp $camp)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->andWhere($expr->eq('camp', $camp));
        $criteria->setMaxResults(1);

        return $this->collaborations->matching($criteria)->isEmpty();
    }

}
