<?php

namespace EcampWeb\Controller\Group;

use EcampWeb\Element\ApiCollectionPaginator;
use EcampCore\Entity\GroupMembership;
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

    public function indexAction()
    {
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headScript()->appendFile('/js/ng-app/paginator.js');

        $managerResourceUrl = $this->url()->fromRoute(
            'api/groups/memberships', array('group' => $this->getGroup()->getId()));

        $managerPaginator = new ApiCollectionPaginator(
            $managerResourceUrl,
            array(
                'role' => GroupMembership::ROLE_MANAGER,
                'status' => GroupMembership::STATUS_ESTABLISHED
            )
        );
        $managerPaginator->setItemsPerPage(10);

        $meberResourceUrl = $this->url()->fromRoute(
            'api/groups/memberships', array('group' => $this->getGroup()->getId()));

        $memberPaginator = new ApiCollectionPaginator(
            $meberResourceUrl,
            array(
                'role' => GroupMembership::ROLE_MEMBER,
                'status' => GroupMembership::STATUS_ESTABLISHED
            )
           );
        $memberPaginator->setItemsPerPage(10);

        $requestsResourceUrl = $this->url()->fromRoute(
            'api/groups/memberships', array('group' => $this->getGroup()->getId()));

        $requestPaginator = new ApiCollectionPaginator(
            $requestsResourceUrl,
            array('status' => GroupMembership::STATUS_REQUESTED)
        );
        $requestPaginator->setItemsPerPage(10);

        $invitationsResourceUrl = $this->url()->fromRoute(
            'api/groups/memberships', array('group' => $this->getGroup()->getId()));

        $invitationPaginator = new ApiCollectionPaginator(
            $invitationsResourceUrl,
            array('status' => GroupMembership::STATUS_INVITED)
        );
        $invitationPaginator->setItemsPerPage(10);

        $editMembershipBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name/default',
            array(
                'group' => $this->getGroup(),
                'controller' => 'Members',
                'action' => 'edit'
            ),
            array('query' => array('user' => ""))
        );

        $acceptRequestBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name/default',
            array(
                'group' => $this->getGroup(),
                'controller' => 'Members',
                'action' => 'acceptRequest'
            ),
            array('query' => array('user' => ''))
        );

        $rejectRequestBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name/default',
            array(
                'group' => $this->getGroup(),
                'controller' => 'Members',
                'action' => 'rejectRequest'
            ),
            array('query' => array('user' => ''))
        );

        $revokeInvitationBaseUrl = $this->url()->fromRoute(
            'web/group-prefix/name/default',
            array(
                'group' => $this->getGroup(),
                'controller' => 'Members',
                'action' => 'revokeInvitation'
            ),
            array('query' => array('user' => ''))
        );

        return array(
            'memberPaginator' => $memberPaginator,
            'managerPaginator' => $managerPaginator,
            'requestPaginator' => $requestPaginator,
            'invitationPaginator' => $invitationPaginator,

            'editMembershipBaseUrl' => $editMembershipBaseUrl,
            'acceptRequestBaseUrl' => $acceptRequestBaseUrl,
            'rejectRequestBaseUrl' => $rejectRequestBaseUrl,
            'revokeInvitationBaseUrl' => $revokeInvitationBaseUrl
        );
    }

    public function editAction()
    {
        $user = $this->getUserRepository()->find($this->params()->fromQuery('user'));
        $membership = $this->getMembershipRepository()->findByGroupAndUser($this->getGroup(), $user);

        return array(
            'membership' => $membership
        );
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
