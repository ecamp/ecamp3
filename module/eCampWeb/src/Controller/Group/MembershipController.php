<?php

namespace eCamp\Web\Controller\Group;

use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\EntityServiceAware\GroupMembershipServiceAware;
use eCamp\Core\EntityServiceTrait\GroupMembershipServiceTrait;
use eCamp\Web\Controller\AbstractBaseController;

class MembershipController extends AbstractBaseController
    implements GroupMembershipServiceAware {
    use GroupMembershipServiceTrait;

    /**
     * @return array|\Zend\View\Model\ViewModel
     * @throws \eCamp\Lib\Acl\NoAccessException
     */
    public function indexAction() {
        $group = $this->params()->fromRoute('group');

        /** @var GroupMembership[] $groupMemberships */
        $groupMemberships = $this->getGroupMembershipService()->fetchAll(['group' => $group]);

        return [
            'group' => $group,
            'groupMemberships' => $groupMemberships
        ];
    }
}
