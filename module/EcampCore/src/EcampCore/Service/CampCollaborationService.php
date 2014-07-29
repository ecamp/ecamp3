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
use EcampCore\Entity\Camp;
use EcampLib\Service\ServiceBase;
use EcampCore\Acl\Privilege;
use EcampCore\Entity\CampCollaboration;
use EcampCore\Repository\CampCollaborationRepository;

/**
 * @method EcampCore\Service\ContributionService Simulate
 */
class CampCollaborationService
    extends ServiceBase
{

    /**
     * @var \EcampCore\Repository\CampCollaborationRepository
     */
    private $campCollaborationRepo;

    public function __construct(CampCollaborationRepository $campCollaborationRepo)
    {
        $this->campCollaborationRepo = $campCollaborationRepo;
    }

    /**
     * @param User   $me
     * @param Camp   $camp
     * @param string $role
     */
    public function requestCollaboration(User $me, Camp $camp, $role = null)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        $campCollaboration = $this->campCollaborationRepo->findByCampAndUser($camp, $me);

        $this->validationAssert(
            is_null($campCollaboration),
            "Collaboration can not be requested"
        );

        $campCollaboration = CampCollaboration::createRequest($me, $camp, $role);
        $this->persist($campCollaboration);
    }

    /**
     * @param User $me
     * @param Camp $camp
     */
    public function revokeRequest(User $me, Camp $camp)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        $campCollaboration = $this->campCollaborationRepo->findByCampAndUser($camp, $me);

        $this->validationAssert(
            $campCollaboration != null && $campCollaboration->isRequest(),
            "There is no open Request"
        );

        $this->remove($campCollaboration);
    }

    /**
     * @param User   $manager
     * @param Group  $group
     * @param User   $user
     * @param string $role
     */
    public function acceptRequest(User $manager, Camp $camp, User $user, $role = null)
    {
        $this->aclRequire($manager, Privilege::USER_ADMINISTRATE);
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $campCollaboration = $this->campCollaborationRepo->findByCampAndUser($camp, $user);

        $this->validationAssert(
            $campCollaboration != null && $campCollaboration->isRequest(),
            "There is no open Request"
        );

        $campCollaboration->acceptRequest($manager, $role);
    }

    /**
     * @param User $manager
     * @param Camp $camp
     * @param User $user
     */
    public function rejectRequest(Camp $camp, User $user)
    {
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $campCollaboration = $this->campCollaborationRepo->findByCampAndUser($camp, $user);

        $this->validationAssert(
            $campCollaboration != null && $campCollaboration->isRequest(),
            "There is no open Request"
        );

        $this->remove($campCollaboration);
    }

    /**
     * @param User   $manager
     * @param Camp   $camp
     * @param User   $user
     * @param string $role
     */
    public function inviteUser(User $manager, Camp $camp, User $user, $role = null)
    {
        $this->aclRequire($manager, Privilege::USER_ADMINISTRATE);
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $campCollaboration = $this->campCollaborationRepo->findByCampAndUser($camp, $user);

        $this->validationAssert(
            is_null($campCollaboration),
            "User can not be invited"
        );

        $campCollaboration = CampCollaboration::createInvitation($user, $camp, $manager, $role);
        $this->persist($campCollaboration);
    }

    /**
     * @param Camp $camp
     * @param User $user
     */
    public function revokeInvitation(Camp $camp, User $user)
    {
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $campCollaboration = $this->campCollaborationRepo->findByCampAndUser($camp, $user);

        $this->validationAssert(
            $campCollaboration != null && $campCollaboration->isInvitation(),
            "There is no open Invitation"
        );

        $this->remove($campCollaboration);
    }

    /**
     * @param User $me
     * @param Camp $camp
     */
    public function acceptInvitation(User $me, Camp $camp)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        $campCollaboration = $this->campCollaborationRepo->findByCampAndUser($camp, $me);

        $this->validationAssert(
            $campCollaboration != null && $campCollaboration->isInvitation(),
            "There is no open Invitation"
        );

        $campCollaboration->acceptInvitation();
    }

    /**
     * @param User $me
     * @param Camp $camp
     */
    public function rejectInvitation(User $me, Camp $camp)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        $campCollaboration = $this->campCollaborationRepo->findByCampAndUser($camp, $me);

        $this->validationAssert(
                $campCollaboration != null && $campCollaboration->isInvitation(),
                "There is no open Invitation"
        );

        $this->remove($campCollaboration);
    }

    /**
     * @param User $me
     * @param Camp $camp
     */
    public function leaveCamp(User $me, Camp $camp)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        $campCollaboration = $this->campCollaborationRepo->findByCampAndUser($camp, $me);

        $this->validationAssert(
            $campCollaboration != null && $campCollaboration->isEstablished(),
            array("camp" => "User is not a Camp Collaborator")
        );

        $this->remove($campCollaboration);
    }

    /**
     * @param Camp $camp
     * @param User $user
     */
    public function kickUser(Camp $camp, User $user)
    {
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $campCollaboration = $this->campCollaborationRepo->findByCampAndUser($camp, $user);

        $this->validationAssert(
            $campCollaboration != null && $campCollaboration->isEstablished(),
            "User is not a Camp Collaborator"
        );

        $this->remove($campCollaboration);
    }

    public function changeRole(Camp $camp, User $user, $role)
    {
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $campCollaboration = $this->campCollaborationRepo->findByCampAndUser($camp, $user);

        $campCollaboration->setRole($role);
    }

}
