<?php

namespace EcampWeb\Controller\Camp;

use Doctrine\Common\Collections\Criteria;
use DoctrineModule\Paginator\Adapter\Selectable as SelectableAdapter;
use EcampCore\Entity\CampCollaboration;
use EcampCore\Entity\User;
use Zend\Http\Response;
use Zend\Paginator\Paginator;

class CollaborationsController extends BaseController
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

    protected function getUserPaginator($query)
    {
        $paginator = $this->getUserRepository()->getSearchResult($query);
        $paginator->setItemCountPerPage(15);
        $paginator->setCurrentPageNumber(1);

        return $paginator;
    }

    protected function getCollaborationsPaginator($status)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('status', $status))
            ->andWhere(Criteria::expr()->eq('camp', $this->getCamp()));

        $adapter = new SelectableAdapter($this->getCollaborationRepository(), $criteria);

        $paginator = new Paginator($adapter);
        $paginator->setItemCountPerPage(15);
        $paginator->setCurrentPageNumber(1);

        return $paginator;
    }

    public function collaborationsAction()
    {
        $page = $this->getRequest()->getQuery('page', 1);

        $paginator = $this->getCollaborationsPaginator(CampCollaboration::STATUS_ESTABLISHED);
        $paginator->setCurrentPageNumber($page);

        return array('paginator' => $paginator);
    }

    public function invitationsAction()
    {
        $page = $this->getRequest()->getQuery('page', 1);

        $paginator = $this->getCollaborationsPaginator(CampCollaboration::STATUS_INVITED);
        $paginator->setCurrentPageNumber($page);

        return array('paginator' => $paginator);
    }

    public function requestsAction()
    {
        $page = $this->getRequest()->getQuery('page', 1);

        $paginator = $this->getCollaborationsPaginator(CampCollaboration::STATUS_REQUESTED);
        $paginator->setCurrentPageNumber($page);

        return array('paginator' => $paginator);
    }

    public function indexAction()
    {
        $collaborationsPaginator = $this->getCollaborationsPaginator(CampCollaboration::STATUS_ESTABLISHED);
        $invitationsPaginator = $this->getCollaborationsPaginator(CampCollaboration::STATUS_INVITED);
        $requestsPaginator = $this->getCollaborationsPaginator(CampCollaboration::STATUS_REQUESTED);

        return array(
            'collaborationsPaginator' => $collaborationsPaginator,
            'invitationsPaginator' => $invitationsPaginator,
            'requestsPaginator' => $requestsPaginator,
        );
    }

    public function inviteSearchAction()
    {
    }

    public function inviteSearchResultAction()
    {
        $page = $this->getRequest()->getQuery('page', 1);
        $query = $this->getRequest()->getQuery('q', '');

        $paginator = $this->getUserPaginator($query);
        $paginator->setCurrentPageNumber($page);

        return array('paginator' => $paginator);
    }

    public function editAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $collaboration = $this->getCollaborationRepository()->findByCampAndUser($this->getCamp(), $user);

        return array(
            'collaboration' => $collaboration
        );
    }

    public function inviteAjaxAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $role = $this->params()->fromQuery('role') ?: CampCollaboration::ROLE_MEMBER;

        $this->getCollaborationService()->inviteUser($this->getMe(), $this->getCamp(), $user, $role);

        $resp = new Response();
        $resp->setStatusCode(Response::STATUS_CODE_200);
        $resp->setContent($role);

        return $resp;
    }

    public function acceptRequestAction()
    {
        $role = $this->params()->fromQuery('role');
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $camp = $this->getCamp();

        $this->getCollaborationService()->acceptRequest($this->getMe(), $camp, $user, $role);

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $camp, 'controller' => 'Collaborations', 'aciton' => 'index')
        );
    }

    public function rejectRequestAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $camp = $this->getCamp();

        $this->getCollaborationService()->rejectRequest($camp, $user);

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $camp, 'controller' => 'Collaborations', 'aciton' => 'index')
        );
    }

    public function revokeInvitationAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $camp = $this->getCamp();

        $this->getCollaborationService()->revokeInvitation($camp, $user);

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $camp, 'controller' => 'Collaborations', 'aciton' => 'index')
        );
    }

    public function changeRoleAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $camp = $this->getCamp();

        $role = $this->params()->fromQuery('role');
        $this->getCollaborationService()->changeRole($camp, $user, $role);

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $camp, 'controller' => 'Collaborations', 'aciton' => 'index')
        );
    }

    public function kickAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $camp = $this->getCamp();

        $this->getCollaborationService()->kickUser($this->getCamp(), $user);

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $camp, 'controller' => 'Collaborations', 'aciton' => 'index')
        );
    }

}
