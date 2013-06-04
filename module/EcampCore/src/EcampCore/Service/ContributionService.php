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
use EcampCore\Entity\UserCamp;
use EcampLib\Service\ServiceBase;

/**
 * @method EcampCore\Service\ContributionService Simulate
 */
class ContributionService
    extends ServiceBase
{

    public function Get($id, $camp_id = null)
    {
        if ($camp_id == null) {
            if (is_string($id)) {
                return $this->repo()->contributorRepository()->find($id);
            }
            if ($id instanceof \EcampCore\Entity\UserCamp) {
                return $id;
            }
        } else {
            $user = $this->service()->userService()->Get($id);
            $camp = $this->service()->campService()->Get($camp_id);

            return $this->repo()->contributorRepository()->findOneBy(array(
                'user' => $user,
                'camp' => $camp
            ));
        }

        return null;
    }

    public function GetCollaborators(Camp $camp)
    {
        return $this->repo()->contributorRepository()->findCollaboratorsByCamp($camp);
    }

    public function GetCamps(User $user)
    {
        return $this->repo()->contributorRepository()->findCollaboratorsByUser($user);
    }

    public function GetRequests(Camp $camp)
    {
        return $this->repo()->contributorRepository()->findOpenRequestsByCamp($camp);
    }

    public function GetInvitations(User $user)
    {
        return $this->repo()->contributorRepository()->findOpenInvitationsByUser($user);
    }

    public function RequestCollaboration(Camp $camp, $role = UserCamp::ROLE_MEMBER)
    {
        $collaboration = new UserCamp();
        $this->persist($collaboration);

        $collaboration->setCamp($camp);
        $collaboration->setUser($this->getContextProvider()->getMe());
        $collaboration->setRequestedRole($role);
        $collaboration->acceptInvitation();

        return $collaboration;
    }

    public function DeleteRequest(UserCamp $request)
    {
        $this->validationAssert(
            $request->isOpenRequest());

        $this->validationAssert(
            $request->getUser() == $this->getContextProvider()->getMe());

        $this->remove($request);
    }

    public function AcceptRequest(UserCamp $request)
    {
        $this->validationAssert(
            $request->isOpenRequest());

        $this->validationAssert(
            $request->getCamp()->isManager($this->getContextProvider()->getMe()));

        $request->acceptRequest($this->getContextProvider()->getMe());
    }

    public function RejectRequest(UserCamp $request)
    {
        $this->validationAssert(
            $request->isOpenRequest());

        $this->validationAssert(
            $request->getCamp()->isManager($this->getContextProvider()->getMe()));

        $this->remove($request);
    }

    public function LeaveCamp(Camp $camp)
    {
        $me = $this->getContextProvider()->getMe();
        $collaboration = $this->Get($me, $camp);

        $this->remove($collaboration);
    }

    public function InviteUser(User $user, $role = UserCamp::ROLE_MEMBER)
    {
        $collaboration = new UserCamp();
        $this->persist($collaboration);

        $collaboration->setCamp($this->getContextProvider()->getCamp());
        $collaboration->setUser($user);
        $collaboration->setRequestedRole($role);
        $collaboration->acceptRequest($this->getContextProvider()->getMe());

        return $collaboration;
    }

    public function DeleteInvitation(UserCamp $invitation)
    {
        $this->validationAssert(
            $invitation->isOpenInvitation());

        $this->validationAssert(
            $invitation->getCamp()->isManager($this->getContextProvider()->getMe()));

        $this->remove($invitation);
    }

    public function AcceptInvitation(UserCamp $invitation)
    {
        $this->validationAssert(
            $invitation->isOpenInvitation());

        $this->validationAssert(
            $invitation->getUser() == $this->getContextProvider()->getMe());

        $invitation->acceptInvitation();
    }

    public function RejectInvitation(UserCamp $invitation)
    {
        $this->validationAssert(
            $invitation->isOpenInvitation());

        $this->validationAssert(
            $invitation->getUser() == $this->getContextProvider()->getMe());

        $this->remove($invitation);
    }

    public function KickOutUser(User $user)
    {
        $camp = $this->getContextProvider()->getCamp();
        $collaboration = $this->Get($user, $camp);

        $this->remove($collaboration);
    }
}
