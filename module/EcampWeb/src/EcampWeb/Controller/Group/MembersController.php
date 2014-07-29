<?php

namespace EcampWeb\Controller\Group;

use Doctrine\Common\Collections\Criteria;
use DoctrineModule\Paginator\Adapter\Selectable as SelectableAdapter;
use EcampWeb\Element\ApiCollectionPaginator;
use EcampCore\Entity\GroupMembership;
use EcampCore\Entity\User;
use Zend\Http\Response;
use Zend\Paginator\Paginator;

class MembersController
    extends BaseController
{
    /**
     * @return \EcampCore\Repository\UserRepository
     */
    private function getUserRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\User');
    }

    /**
     * @return \EcampCore\Repository\GroupMembershipRepository
     */
    private function getMembershipRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\GroupMembership');
    }

    /**
     * @return \EcampCore\Service\GroupMembershipService
     */
    private function getMembershipService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\GroupMembership');
    }

    protected function getMembersPaginator($status)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('status', $status))
            ->andWhere(Criteria::expr()->eq('group', $this->getGroup()));

        $adapter = new SelectableAdapter($this->getMembershipRepository(), $criteria);

        $paginator = new Paginator($adapter);
        $paginator->setItemCountPerPage(15);
        $paginator->setCurrentPageNumber(1);

        return $paginator;
    }

    public function membersAction()
    {
        $page = $this->getRequest()->getQuery('page', 1);

        $paginator = $this->getMembersPaginator(GroupMembership::STATUS_ESTABLISHED);
        $paginator->setCurrentPageNumber($page);

        return array('paginator' => $paginator);
    }

    public function invitationsAction()
    {
        $page = $this->getRequest()->getQuery('page', 1);

        $paginator = $this->getMembersPaginator(GroupMembership::STATUS_INVITED);
        $paginator->setCurrentPageNumber($page);

        return array('paginator' => $paginator);
    }

    public function requestsAction()
    {
        $page = $this->getRequest()->getQuery('page', 1);

        $paginator = $this->getMembersPaginator(GroupMembership::STATUS_REQUESTED);
        $paginator->setCurrentPageNumber($page);

        return array('paginator' => $paginator);
    }

    public function indexAction()
    {

        $membersPaginator = $this->getMembersPaginator(GroupMembership::STATUS_ESTABLISHED);
        $invitationsPaginator = $this->getMembersPaginator(GroupMembership::STATUS_INVITED);
        $requestsPaginator = $this->getMembersPaginator(GroupMembership::STATUS_REQUESTED);

        return array(
            'membersPaginator' => $membersPaginator,
            'invitationsPaginator' => $invitationsPaginator,
            'requestsPaginator' => $requestsPaginator,
        );
    }

    public function inviteSearchAction()
    {
        return array();
    }

    public function editAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $membership = $this->getMembershipRepository()->findByGroupAndUser($this->getGroup(), $user);

        return array(
            'membership' => $membership
        );
    }

    public function searchAction()
    {
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headScript()->appendFile(
            $this->getRequest()->getBasePath() . '/js/ng-app/member/search-result.js');

        $searchResourceUrl = $this->url()->fromRoute('api/search/user', array());

        $searchPaginator = new ApiCollectionPaginator(
            $searchResourceUrl,
            array(
                'status' => User::STATE_ACTIVATED,
                'showMembershipOfGroup' => $this->getGroup()->getId()
            )
        );
        $searchPaginator->setItemsPerPage(12);

        $inviteAsMemberBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name/default',
            array(
                'group' => $this->getGroup(),
                'controller' => 'Members',
                'action' => 'inviteAjax'
            ),
            array('query' => array('role' => GroupMembership::ROLE_MEMBER, 'user' => ''))
        );

        $inviteAsManagerBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name/default',
            array(
                'group' => $this->getGroup(),
                'controller' => 'Members',
                'action' => 'inviteAjax'
            ),
            array('query' => array('role' => GroupMembership::ROLE_MANAGER, 'user' => ''))
        );

        return array(
            'searchPaginator' => $searchPaginator,

            'inviteAsMemberBaseUrl' => $inviteAsMemberBaseUrl,
            'inviteAsManagerBaseUrl' => $inviteAsManagerBaseUrl,
        );
    }

    public function inviteAjaxAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $role = $this->params()->fromQuery('role') ?: GroupMembership::ROLE_MEMBER;

        $this->getMembershipService()->inviteUser($this->getMe(), $this->getGroup(), $user, $role);

        $resp = new Response();
        $resp->setStatusCode(Response::STATUS_CODE_200);
        $resp->setContent($role);

        return $resp;
    }

    public function kickAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));

        $this->getMembershipService()->kickUser($this->getGroup(), $user);

        return $this->redirect()->toRoute('web/group-prefix/name/default',
            array('group' => $this->getGroup(), 'controller'=>'Members', 'action'=>'index')
        );
    }

    public function changeRoleAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $group = $this->getGroup();

        $role = $this->params()->fromQuery('role');
        $this->getMembershipService()->changeRole($group, $user, $role);

        return $this->redirect()->toRoute('web/group-prefix/name/default',
            array('group' => $this->getGroup(), 'controller'=>'Members', 'action'=>'index')
        );
    }

    public function acceptRequestAction()
    {
        $role = $this->params()->fromQuery('role');
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $group = $this->getGroup();

        $this->getMembershipService()->acceptRequest($this->getMe(), $group, $user, $role);

        return $this->redirect()->toRoute('web/group-prefix/name/default',
            array('group' => $this->getGroup(), 'controller'=>'Members', 'action'=>'index')
        );
    }

    public function rejectRequestAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $group = $this->getGroup();

        $this->getMembershipService()->rejectRequest($group, $user);

        return $this->redirect()->toRoute('web/group-prefix/name/default',
            array('group' => $this->getGroup(), 'controller'=>'Members', 'action'=>'index')
        );
    }

    public function revokeInvitationAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $group = $this->getGroup();

        $this->getMembershipService()->revokeInvitation($group, $user);

        return $this->redirect()->toRoute('web/group-prefix/name/default',
            array('group' => $this->getGroup(), 'controller'=>'Members', 'action'=>'index')
        );
    }

}
