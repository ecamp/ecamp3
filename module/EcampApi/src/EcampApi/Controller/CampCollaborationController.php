<?php

namespace EcampApi\Controller;

use EcampCore\Controller\AbstractBaseController;
use EcampCore\Entity\CampCollaboration;

class CampCollaborationController extends AbstractBaseController
{

    /**
     * @return \EcampCore\Service\CampCollaborationService
     */
    protected function getCollaborationService(){
        return $this->getServiceLocator()->get('EcampCore\Service\CampCollaboration');
    }

    /**
     * @return \EcampCore\Entity\Camp
     */
    protected function getCamp(){
        $campId = $this->params()->fromRoute('camp');
        return ($campId != null) ? $this->getCampService()->Get($campId) : null;
    }

    /**
     * @return \EcampCore\Entity\User
     */
    protected function getCollaborator(){
        $collaboratorId = $this->params()->fromRoute('collaborator');
        return ($collaboratorId != null) ? $this->getUserService()->Get($collaboratorId) : null;
    }

    protected function getRole(){
        return $this->params()->fromQuery('role', CampCollaboration::ROLE_MEMBER);
    }


    public function requestAction()
    {
        $this->getCollaborationService()->requestCollaboration($this->getCollaborator(), $this->getCamp(), $this->getRole());
        return $this->redirectToApi();
    }

    public function revokeRequestAction()
    {
        $this->getCollaborationService()->revokeRequest($this->getCollaborator(), $this->getCamp());
        return $this->redirectToApi();
    }

    public function acceptRequestAction()
    {
        $this->getCollaborationService()->acceptRequest($this->getMe(), $this->getCamp(), $this->getCollaborator(), $this->getRole());
        return $this->redirectToApi();
    }

    public function rejectRequestAction()
    {
        $this->getCollaborationService()->rejectRequest($this->getCamp(), $this->getCollaborator());
        return $this->redirectToApi();
    }


    public function inviteAction()
    {
        $this->getCollaborationService()->inviteUser($this->getMe(), $this->getCamp(), $this->getCollaborator(), $this->getRole());
        return $this->redirectToApi();
    }

    public function revokeInvitationAction()
    {
        $this->getCollaborationService()->revokeInvitation($this->getCamp(), $this->getCollaborator());
        return $this->redirectToApi();
    }

    public function acceptInvitationAction()
    {
        $this->getCollaborationService()->acceptInvitation($this->getCollaborator(), $this->getCamp());
        return $this->redirectToApi();
    }

    public function rejectInvitationAction()
    {
        $this->getCollaborationService()->rejectInvitation($this->getCollaborator(), $this->getCamp());
        return $this->redirectToApi();
    }


    public function kickAction()
    {
        $this->getCollaborationService()->kickUser($this->getCamp(), $this->getCollaborator());
        return $this->redirectToApi();
    }

    public function leaveAction()
    {
        $this->getCollaborationService()->leaveCamp($this->getCollaborator(), $this->getCamp());
        return $this->redirectToApi();
    }


    protected function redirectToApi(){
        return $this->redirect()->toRoute(
            'api/camps/collaborators',
            array(
                'camp' => $this->params()->fromRoute('camp'),
                'collaborator' => $this->params()->fromRoute('collaborator')
            )
        );
    }

}