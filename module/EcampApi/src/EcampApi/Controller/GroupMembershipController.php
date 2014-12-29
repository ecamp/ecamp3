<?php

namespace EcampApi\Controller;

use EcampCore\Controller\AbstractBaseController;
use EcampCore\Entity\GroupMembership;

class GroupMembershipController extends AbstractBaseController
{

    /**
     * @return \EcampCore\Service\GroupMembershipService
     */
    protected function getMembershipService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\GroupMembership');
    }

    /**
     * @return \EcampCore\Entity\Group
     */
    protected function getGroup()
    {
        $groupId = $this->params()->fromRoute('group');

        return ($groupId != null) ? $this->getGroupService()->Get($groupId) : null;
    }

    /**
     * @return \EcampCore\Entity\User
     */
    protected function getMember()
    {
        $memberId = $this->params()->fromRoute('member');

        return ($memberId != null) ? $this->getUserService()->Get($memberId) : null;
    }

    protected function getRole()
    {
        return $this->params()->fromQuery('role', GroupMembership::ROLE_MEMBER);
    }

    public function requestAction()
    {
        $this->getMembershipService()->requestMembership($this->getMember(), $this->getGroup(), $this->getRole());

        return $this->redirectToApi();
    }

    public function revokeRequestAction()
    {
        $this->getMembershipService()->revokeRequest($this->getMember(), $this->getGroup());

        return $this->redirectToApi();
    }

    public function acceptRequestAction()
    {
        $this->getMembershipService()->acceptRequest($this->getMe(), $this->getGroup(), $this->getMember(), $this->getRole());

        return $this->redirectToApi();
    }

    public function rejectRequestAction()
    {
        $this->getMembershipService()->rejectRequest($this->getGroup(), $this->getMember());

        return $this->redirectToApi();
    }

    public function inviteAction()
    {
        $this->getMembershipService()->inviteUser($this->getMe(), $this->getGroup(), $this->getMember(), $this->getRole());

        return $this->redirectToApi();
    }

    public function revokeInvitationAction()
    {
        $this->getMembershipService()->revokeInvitation($this->getGroup(), $this->getMember());

        return $this->redirectToApi();
    }

    public function acceptInvitationAction()
    {
        $this->getMembershipService()->acceptInvitation($this->getMember(), $this->getGroup());

        return $this->redirectToApi();
    }

    public function rejectInvitationAction()
    {
        $this->getMembershipService()->rejectInvitation($this->getMember(), $this->getGroup());

        return $this->redirectToApi();
    }

    public function kickAction()
    {
        $this->getMembershipService()->kickUser($this->getGroup(), $this->getMember());

        return $this->redirectToApi();
    }

    public function leaveAction()
    {
        $this->getMembershipService()->leaveGroup($this->getMember(), $this->getGroup());

        return $this->redirectToApi();
    }

    protected function redirectToApi()
    {
        return $this->redirect()->toRoute(
            'api/groups/members',
            array(
                'group' => $this->params()->fromRoute('group'),
                'member' => $this->params()->fromRoute('member')
            )
        );
    }

}
