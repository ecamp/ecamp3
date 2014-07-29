<?php

namespace EcampWeb\Controller\Group;

use Doctrine\Common\Collections\Criteria;
use DoctrineModule\Paginator\Adapter\Selectable as SelectableAdapter;
use EcampCore\Entity\GroupMembership;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;

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
     * @return \EcampCore\Repository\CampRepository
     */
    private function getCampRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Camp');
    }

    /**
     * @return \EcampCore\Service\GroupMembershipService
     */
    private function getMembershipService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\GroupMembership');
    }

    protected function getSubgroupsPaginator()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('parent', $this->getGroup()));

        $adapter = new SelectableAdapter($this->getGroupRepository(), $criteria);

        $paginator = new Paginator($adapter);
        $paginator->setItemCountPerPage(15);
        $paginator->setCurrentPageNumber(1);

        return $paginator;
    }

    protected function getUpcomingCampsPaginator()
    {
        $upcomingCamps = $this->getCampRepository()->findUpcomingCamps($this->getGroup());

        $adapter = new ArrayAdapter($upcomingCamps);

        $paginator = new Paginator($adapter);
        $paginator->setItemCountPerPage(15);
        $paginator->setCurrentPageNumber(1);

        return $paginator;
    }

    public function subgroupsAction()
    {
        $page = $this->getRequest()->getQuery('page', 1);

        $paginator = $this->getSubgroupsPaginator();
        $paginator->setCurrentPageNumber($page);

        return array('paginator' => $paginator);
    }

    public function upcomingCampsAction()
    {
        $page = $this->getRequest()->getQuery('page', 1);

        $paginator = $this->getUpcomingCampsPaginator();
        $paginator->setCurrentPageNumber($page);

        return array('paginator' => $paginator);
    }

    public function indexAction()
    {
        $subgroupsPaginator = $this->getSubgroupsPaginator();
        $upcomingCampsPaginator = $this->getUpcomingCampsPaginator();

        $myMembership = $this->getMembershipRepository()
            ->findByGroupAndUser($this->getGroup(), $this->getMe());

        return array(
            'subgroupsPaginator' => $subgroupsPaginator,
            'upcomingCampsPaginator' => $upcomingCampsPaginator,
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
