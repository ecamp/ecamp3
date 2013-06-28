<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace EcampCore\Service;

use EcampCore\Entity\User;
use EcampCore\Entity\UserRelationship;
use EcampLib\Service\ServiceBase;

/**
 * @method EcampCore\Service\RelationshipService Simulate
 */
class RelationshipService
    extends ServiceBase
{

    public function Get($id, $user_id = null)
    {
        if ($user_id == null) {
            if ($id instanceof UserRelationship) {
                return $id;
            }

            return $this->repo()->userRelationshipRepository()->find($id);
        } else {
            $user1 = $this->service()->userService()->Get($id);
            $user2 = $this->service()->userService()->Get($user_id);

            return $this->repo()->userRelationshipRepository()->findByUsers($user1, $user2);
        }
    }

    /**
     * @return Doctrine\Common\Collection\ArrayCollection
     */
    public function GetFriends()
    {
        $user = $this->service()->userService()->Get();

        return $this->repo()->userRepository()->findFriends($user);
    }

    /**
     * @return Doctrine\Common\Collection\ArrayCollection
     */
    public function GetRequests()
    {
        $user = $this->service()->userService()->Get();

        return $this->repo()->userRepository()->findFriendRequests($user);
    }

    /**
     * @return Doctrine\Common\Collection\ArrayCollection
     */
    public function GetInvitations()
    {
        $user = $this->service()->userService()->Get();

        return $this->repo()->userRepository()->findFriendInvitations($user);
    }

    /**
     * @param  User             $toUser
     * @return UserRelationship
     */
    public function RequestRelationship(User $toUser)
    {
        $user = $this->service()->userService()->Get();
        $ur = $this->repo()->userRelationshipRepository()->findByUsers($user, $toUser);

        $this->validationAssert(
            $ur == null,
            "There is already a relationship between these users");

        $ur = new UserRelationship($user, $toUser);
        $this->persist($ur);

        return $ur;
    }

    /**
     * @param User $toUser
     */
    public function DeleteRequest(User $toUser)
    {
        $user = $this->service()->userService()->Get();
        $ur = $this->repo()->userRelationshipRepository()->findByUsers($user, $toUser);

        $this->validationAssert(
            $ur != null && $ur->getCounterpart() == null,
            "There is no open request to delete");

        if ($ur) {
            $this->remove($ur);
        }
    }

    /**
     * @param  User             $fromUser
     * @return UserRelationship
     */
    public function AcceptInvitation(User $fromUser)
    {
        $user = $this->service()->userService()->Get();
        $ur = $this->repo()->userRelationshipRepository()->findByUsers($fromUser, $user);

        $this->validationAssert(
            $ur && $ur->getCounterpart() == null,
            "There is no open invitation to accept");

        $cp = new UserRelationship($user, $fromUser);
        $this->persist($cp);

        UserRelationship::Link($ur, $cp);

        return $cp;
    }

    /**
     * @param User $fromUser
     */
    public function RejectInvitation(User $fromUser)
    {
        $user = $this->service()->userService()->Get();
        $ur = $this->repo()->userRelationshipRepository()->findByUsers($fromUser, $user);

        $this->validationAssert(
            $ur && $ur->getCounterpart() == null,
            "There is no open invitation to delete");

        if ($ur) {
            $this->remove($ur);
        }
    }

    public function CancelRelationship(User $withUser)
    {
        $user = $this->service()->userService()->Get();
        $ur = $this->repo()->userRelationshipRepository()->findByUsers($user, $withUser);

        $this->validationAssert(
            $ur && $ur->getCounterpart(),
            "There is no relationship to be canceled");

        if ($ur && $ur->getCounterpart()) {
            $this->remove($ur->getCounterpart());
            $this->remove($ur);
        }
    }

}
