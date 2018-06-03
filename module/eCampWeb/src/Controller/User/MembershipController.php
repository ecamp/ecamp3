<?php

namespace eCamp\Web\Controller\User;

use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\EntityService\GroupMembershipService;
use eCamp\Web\Controller\AbstractBaseController;

class MembershipController extends AbstractBaseController
{
    /** @var GroupMembershipService */
    private $groupMembershipService;


    public function __construct(GroupMembershipService $groupMembershipService)
    {
        $this->groupMembershipService = $groupMembershipService;
    }

    /**
     * @return array|\Zend\View\Model\ViewModel
     * @throws \eCamp\Lib\Acl\NoAccessException
     */
    public function indexAction()
    {
        $user = $this->params()->fromRoute('user');

        /** @var GroupMembership[] $groupMemberships */
        $groupMemberships = $this->groupMembershipService->fetchAll(['user' => $user]);

        return [
            'user' => $user,
            'groupMemberships' => $groupMemberships
        ];
    }
}
