<?php

namespace EcampWeb\Controller\Camp;

use EcampCore\Entity\CampCollaboration;
class IndexController
    extends BaseController
{

    /**
     * @return \EcampCore\Repository\CampCollaborationRepository
     */
    private function getCollaborationRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\CampCollaboration');
    }

    /**
     * @return \EcampCore\Service\CampCollaborationService
     */
    private function getCollaborationService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\CampCollaboration');
    }

    public function indexAction()
    {
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headScript()->appendFile($this->getRequest()->getBasePath() . '/js/ng-app/paginator.js');

        $myCollaboration = $this->getCollaborationRepository()->findByCampAndUser($this->getCamp(), $this->getMe());

        return array(
            'myCollaboration' => $myCollaboration
        );
    }

    public function requestCollaborationAction()
    {
        $role = $this->params()->fromQuery('role') ?: CampCollaboration::ROLE_MEMBER;
        $this->getCollaborationService()->requestCollaboration($this->getMe(), $this->getCamp(), $role);

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function revokeRequestAction()
    {
        $this->getCollaborationService()->revokeRequest($this->getMe(), $this->getCamp());

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function acceptInvitationAction()
    {
        $this->getCollaborationService()->acceptInvitation($this->getMe(), $this->getCamp());

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function rejectInvitationAction()
    {
        $this->getCollaborationService()->rejectInvitation($this->getMe(), $this->getCamp());

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function leaveCampAction()
    {
        $this->getCollaborationService()->leaveCamp($this->getMe(), $this->getCamp());

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp(), 'controller'=>'Index', 'action'=>'index')
        );
    }

}
