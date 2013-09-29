<?php

namespace EcampWeb\Controller\Group;

use EcampWeb\Element\ApiCollectionPaginator;
use EcampCore\Entity\GroupMembership;

class IndexController
    extends BaseController
{

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

        $subgroupResourceUrl = $this->url()->fromRoute(
            'api/groups/groups', array('group' => $this->getGroup()->getId()));
        $campsResourceUrl = $this->url()->fromRoute(
            'api/groups/camps', array('group' => $this->getGroup()->getId()));

        $subgroupPaginator = new ApiCollectionPaginator($subgroupResourceUrl);
        $subgroupPaginator->setItemsPerPage(10);

        $campsPaginator = new ApiCollectionPaginator($campsResourceUrl);
        $campsPaginator->setItemsPerPage(10);

        $myMembership = $this->getMembershipRepository()
            ->findByGroupAndUser($this->getGroup(), $this->getMe());

        return array(
            'subgroupPaginator' => $subgroupPaginator,
            'campsPaginator' => $campsPaginator,
            'myMembership' => $myMembership
        );
    }

    public function requestMembershipAction()
    {
        $role = $this->params()->fromQuery('role') ?: GroupMembership::ROLE_MEMBER;
        $this->getMembershipService()->requestMembership($this->getMe(), $this->getGroup(), $role);

        return $this->redirect()->toRoute('web/group-prefix/name/default',
            array('group' => $this->getGroup(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function revokeRequestAction()
    {
        $this->getMembershipService()->revokeRequest($this->getMe(), $this->getGroup());

        return $this->redirect()->toRoute('web/group-prefix/name/default',
            array('group' => $this->getGroup(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function acceptInvitationAction()
    {
        $this->getMembershipService()->acceptInvitation($this->getMe(), $this->getGroup());

        return $this->redirect()->toRoute('web/group-prefix/name/default',
            array('group' => $this->getGroup(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function rejectInvitationAction()
    {
        $this->getMembershipService()->rejectInvitation($this->getMe(), $this->getGroup());

        return $this->redirect()->toRoute('web/group-prefix/name/default',
            array('group' => $this->getGroup(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function leaveGroupAction()
    {
        $this->getMembershipService()->leaveGroup($this->getMe(), $this->getGroup());

        return $this->redirect()->toRoute('web/group-prefix/name/default',
                array('group' => $this->getGroup(), 'controller'=>'Index', 'action'=>'index')
        );
    }
}
