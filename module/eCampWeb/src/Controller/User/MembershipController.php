<?php

namespace eCamp\Web\Controller\User;

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
        $user = $this->params()->fromRoute('user');

        /** @var GroupMembership[] $groupMemberships */
        $groupMemberships = $this->getGroupMembershipService()->fetchAll(['user' => $user]);

        return [
            'user' => $user,
            'groupMemberships' => $groupMemberships
        ];
    }
}
