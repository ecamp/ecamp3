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

use EcampLib\Service\ServiceBase;

use EcampCore\Entity\User;
use EcampCore\Entity\UserRelationship;
use EcampCore\Repository\UserRelationshipRepository;
use EcampCore\Acl\Privilege;

class RelationshipService
    extends ServiceBase
{
    /**
     * @var UserRelationshipRepository
     */
    private $userRelationshipRepo;

    public function __construct(UserRelationshipRepository $userRelationshipRepo)
    {
        $this->userRelationshipRepo = $userRelationshipRepo;
    }

    /**
     * User $me requests for Friendship with User $target.
     *
     * @param  User    $me
     * @param  User    $target
     * @return boolean
     */
    public function RequestFriendship(User $me, User $target)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        if (! $me->userRelationship()->canSendFriendshipRequest($target)) {
            // can not send FreindshipRequest
            return false;
        }

        $ur = new UserRelationship($me, $target);
        $this->persist($ur);

        return true;
    }

    /**
     * User $me revokes the friendship request to User $target.
     *
     * @param  User    $me
     * @param  User    $target
     * @return boolean
     */
    public function RevokeFriendshipRequest(User $me, User $target)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        if (! $me->userRelationship()->hasSentFriendshipRequestTo($target)) {
            // there is no open FriendshipRequest
            return false;
        }

        $ur = $this->userRelationshipRepo->findByUsers($me, $target);

        $this->remove($ur);

        return true;
    }

    /**
     * User $me accepts the friendship request from User $requestor.
     *
     * @param  User    $me
     * @param  User    $requestor
     * @return boolean
     */
    public function AcceptFriendshipRequest(User $me, User $requestor)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        if (! $me->userRelationship()->hasReceivedFriendshipRequestFrom($requestor)) {
            // there is no open FriendshipRequest to accept
            return false;
        }

        $cp = new UserRelationship($me, $requestor);
        $this->persist($cp);

        $ur = $this->userRelationshipRepo->findByUsers($requestor, $me);
        UserRelationship::Link($ur, $cp);

        return true;
    }

    /**
     * User $me rejects the friendship request from User $requestor
     *
     * @param  User    $me
     * @param  User    $requestor
     * @return boolean
     */
    public function RejectFriendshipRequest(User $me, User $requestor)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        if (! $me->userRelationship()->hasReceivedFriendshipRequestFrom($requestor)) {
            // there is no open FriendshipRequest to reject
            return false;
        }

        $ur = $this->userRelationshipRepo->findByUsers($requestor, $me);
        $this->remove($ur);

        return true;
    }

    /**
     * User $me terminates the friendship with User $friend
     *
     * @param  User    $me
     * @param  User    $friend
     * @return boolean
     */
    public function TerminateFriendship(User $me, User $friend)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        if (! $me->userRelationship()->isFriend($friend)) {
            // there is no established friendship to terminate
            return false;
        }

        $ur = $this->userRelationshipRepo->findByUsers($me, $friend);
        $cp = $ur->getCounterpart();

        $this->remove($ur);
        $this->remove($cp);

        return true;
    }

}
