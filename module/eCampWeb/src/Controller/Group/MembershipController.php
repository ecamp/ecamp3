<?php

namespace eCamp\Web\Controller\Group;

use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\EntityService\GroupMembershipService;
use eCamp\Core\EntityServiceAware\GroupMembershipServiceAware;
use eCamp\Core\EntityServiceTrait\GroupMembershipServiceTrait;
use eCamp\Web\Controller\AbstractBaseController;

class MembershipController extends AbstractBaseController {

    private $groupMembershipService;

    public function __construct(GroupMembershipService $groupMembershipService) {
        $this->groupMembershipService = $groupMembershipService;
    }

    /**
     * @return array|\Zend\View\Model\ViewModel
     * @throws \eCamp\Lib\Acl\NoAccessException
     */
    public function indexAction() {
        $group = $this->params()->fromRoute('group');

        /** @var GroupMembership[] $groupMemberships */
        $groupMemberships = $this->groupMembershipService->fetchAll(['group' => $group]);

        return [
            'group' => $group,
            'groupMemberships' => $groupMemberships
        ];
    }
}
