<?php

namespace EcampWeb\Controller\Camp;

use EcampCore\Entity\Camp;
use EcampCore\Entity\CampCollaboration;
use EcampCore\Entity\User;
use Zend\View\Model\ViewModel;

class CollaborationHelperController
    extends BaseController
{
    /**
     * @return \EcampCore\Service\CampCollaborationService
     */
    private function getCampCollaborationService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\CampCollaboration');
    }

    public function requestCollaborationAction()
    {
        $camp = $this->getRouteCamp();
        $role = $this->params()->fromQuery('role', CampCollaboration::ROLE_MEMBER);

        try {
            $this->getCampCollaborationService()->requestCollaboration($this->getMe(), $camp, $role);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($camp);
    }

    public function revokeRequestAction()
    {
        $camp = $this->getRouteCamp();

        try {
            $this->getCampCollaborationService()->revokeRequest($this->getMe(), $camp);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($camp);
    }

    public function rejectRequestAction()
    {
        $camp = $this->getRouteCamp();
        $user = $this->getQueryUser();

        try {
            $this->getCampCollaborationService()->rejectRequest($camp, $user);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($camp, $user);
    }

    public function acceptRequestAction()
    {
        $camp = $this->getRouteCamp();
        $user = $this->getQueryUser();
        $role = $this->params()->fromQuery('role', CampCollaboration::ROLE_MEMBER);

        try {
            $this->getCampCollaborationService()->acceptRequest($this->getMe(), $camp, $user, $role);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($camp, $user);
    }

    public function inviteUserAction()
    {
        $camp = $this->getRouteCamp();
        $user = $this->getQueryUser();
        $role = $this->params()->fromQuery('role', CampCollaboration::ROLE_MEMBER);

        try {
            $this->getCampCollaborationService()->inviteUser($this->getMe(), $camp, $user, $role);
        } catch (\Exception $ex) {
            var_dump($ex);
        }

        return $this->createViewModel($camp, $user);
    }

    public function revokeInvitationAction()
    {
        $camp = $this->getRouteCamp();
        $user = $this->getQueryUser();

        try {
            $this->getCampCollaborationService()->revokeInvitation($camp, $user);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($camp, $user);
    }

    public function rejectInvitationAction()
    {
        $camp = $this->getRouteCamp();

        try {
            $this->getCampCollaborationService()->rejectInvitation($this->getMe(), $camp);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($camp);
    }

    public function acceptInvitationAction()
    {
        $camp = $this->getRouteCamp();

        try {
            $this->getCampCollaborationService()->acceptInvitation($this->getMe(), $camp);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($camp);
    }

    public function leaveCampAction()
    {
        $camp = $this->getRouteCamp();

        try {
            $this->getCampCollaborationService()->leaveCamp($this->getMe(), $camp);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($camp);
    }

    public function kickUserAction()
    {
        $camp = $this->getRouteCamp();
        $user = $this->getQueryUser();

        try {
            $this->getCampCollaborationService()->kickUser($camp, $user);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($camp, $user);
    }

    private function createViewModel(Camp $camp, User $user = null)
    {
        $size = $this->params()->fromQuery('size', '');

        $viewModel = new ViewModel();
        $viewModel->setTemplate('ecamp-web/helper/collaboration/collaboration.twig');
        $viewModel->setVariable('camp', $camp);
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('size', $size);

        return $viewModel;
    }

}
