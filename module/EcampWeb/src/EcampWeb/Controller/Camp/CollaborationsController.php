<?php

namespace EcampWeb\Controller\Camp;

use EcampWeb\Element\ApiCollectionPaginator;
use EcampCore\Entity\CampCollaboration;
use EcampCore\Entity\User;
use Zend\Http\Response;

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

    public function indexAction()
    {
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headScript()->appendFile(
            $this->getRequest()->getBasePath() . '/js/ng-app/paginator.js');

        $collaborationResourceUrl = $this->url()->fromRoute(
            'api/camps/collaborations', array('camp' => $this->getCamp()->getId()));

        $collaborationPaginator = new ApiCollectionPaginator(
            $collaborationResourceUrl,
            array('status' => CampCollaboration::STATUS_ESTABLISHED)
        );

        $requestResourceUrl = $this->url()->fromRoute(
            'api/camps/collaborations', array('camp' => $this->getCamp()->getId()));

        $requestPaginator = new ApiCollectionPaginator(
            $requestResourceUrl,
            array('status' => CampCollaboration::STATUS_REQUESTED)
        );

        $invitationResourceUrl = $this->url()->fromRoute(
            'api/camps/collaborations', array('camp' => $this->getCamp()->getId()));

        $invitationPaginator = new ApiCollectionPaginator(
            $invitationResourceUrl,
            array('status' => CampCollaboration::STATUS_INVITED)
        );

        $acceptRequestBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name+camp/default',
            array(
                'group' => $this->getCamp()->getGroup(),
                'camp' => $this->getCamp(),
                'controller' => 'Collaborations',
                'action' => 'acceptRequest'
            ),
            array('query' => array('user' => ''))
        );

        $rejectRequestBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name+camp/default',
            array(
                'group' => $this->getCamp()->getGroup(),
                'camp' => $this->getCamp(),
                'controller' => 'Collaborations',
                'action' => 'rejectRequest'
            ),
            array('query' => array('user' => ''))
        );

        $revokeInvitationBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name+camp/default',
            array(
                'group' => $this->getCamp()->getGroup(),
                'camp' => $this->getCamp(),
                'controller' => 'Collaborations',
                'action' => 'revokeInvitation'
            ),
            array('query' => array('user' => ''))
        );

        $editCollaborationBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name+camp/default',
            array(
                'group' => $this->getCamp()->getGroup(),
                'camp' => $this->getCamp(),
                'controller' => 'Collaborations',
                'action' => 'edit'
            ),
            array('query' => array('user' => ''))
        );

        return array(
            'collaborationPaginator' => $collaborationPaginator,
            'requestPaginator' => $requestPaginator,
            'invitationPaginator' => $invitationPaginator,

            'acceptRequestBaseUrl' => $acceptRequestBaseUrl,
            'rejectRequestBaseUrl' => $rejectRequestBaseUrl,
            'revokeInvitationBaseUrl' => $revokeInvitationBaseUrl,
            'editCollaborationBaseUrl' => $editCollaborationBaseUrl,
        );
    }

    public function searchAction()
    {
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headScript()->appendFile(
            $this->getRequest()->getBasePath() . '/js/ng-app/paginator.js');
        $renderer->headScript()->appendFile(
            $this->getRequest()->getBasePath() . '/js/ng-app/collaboration/search-result.js');

        $searchResourceUrl = $this->url()->fromRoute('api/search/user', array());

        $searchPaginator = new ApiCollectionPaginator(
            $searchResourceUrl,
            array(
                'status' => User::STATE_ACTIVATED,
                'showCollaborationOfCamp' => $this->getCamp()->getId()
            )
        );
        $searchPaginator->setItemsPerPage(12);

        $inviteAsMemberBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name+camp/default',
            array(
                'group' => $this->getCamp()->getGroup(),
                'camp' => $this->getCamp(),
                'controller' => 'Collaborations',
                'action' => 'inviteAjax'
            ),
            array('query' => array('role' => CampCollaboration::ROLE_MEMBER, 'user' => ''))
        );

        $inviteAsManagerBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name/default',
            array(
                'group' => $this->getCamp()->getGroup(),
                'camp' => $this->getCamp(),
                'controller' => 'Collaborations',
                'action' => 'inviteAjax'
            ),
            array('query' => array('role' => CampCollaboration::ROLE_MANAGER, 'user' => ''))
        );

        return array(
            'searchPaginator' => $searchPaginator,

            'inviteAsMemberBaseUrl' => $inviteAsMemberBaseUrl,
            'inviteAsManagerBaseUrl' => $inviteAsManagerBaseUrl,
        );
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

        return $this->redirect()->toRoute('web/group-prefix/name+camp/default',
            array('group' => $camp->getGroup(), 'camp' => $camp, 'controller' => 'Collaborations', 'aciton' => 'index')
        );
    }

    public function rejectRequestAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $camp = $this->getCamp();

        $this->getCollaborationService()->rejectRequest($camp, $user);

        return $this->redirect()->toRoute('web/group-prefix/name+camp/default',
            array('group' => $camp->getGroup(), 'camp' => $camp, 'controller' => 'Collaborations', 'aciton' => 'index')
        );
    }

    public function revokeInvitationAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $camp = $this->getCamp();

        $this->getCollaborationService()->revokeInvitation($camp, $user);

        return $this->redirect()->toRoute('web/group-prefix/name+camp/default',
                array('group' => $camp->getGroup(), 'camp' => $camp, 'controller' => 'Collaborations', 'aciton' => 'index')
        );
    }

    public function changeRoleAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $camp = $this->getCamp();

        $role = $this->params()->fromQuery('role');
        $this->getCollaborationService()->changeRole($camp, $user, $role);

        return $this->redirect()->toRoute('web/group-prefix/name+camp/default',
            array('group' => $camp->getGroup(), 'camp' => $camp, 'controller' => 'Collaborations', 'aciton' => 'index')
        );
    }

    public function kickAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $camp = $this->getCamp();

        $this->getCollaborationService()->kickUser($this->getCamp(), $user);

        return $this->redirect()->toRoute('web/group-prefix/name+camp/default',
            array('group' => $camp->getGroup(), 'camp' => $camp, 'controller' => 'Collaborations', 'aciton' => 'index')
        );
    }

}
