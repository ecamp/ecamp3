<?php

namespace EcampWeb\Controller;

use EcampLib\Controller\AbstractBaseController;
use EcampCore\Entity\GroupMembership;

class IndexController
    extends AbstractBaseController
{

    /**
     * @return \EcampCore\Repository\CampRepository
     */
    private function getCampRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Camp');
    }

    /**
     * @return \EcampCore\Repository\GroupMembershipRepository
     */
    private function getGroupMembershipRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\GroupMembership');
    }

    /**
     * @return \EcampCore\Repository\UserRelationshipRepository
     */
    private function getUserRelationshipRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\UserRelationship');
    }

    public function indexAction()
    {
        $me = $this->getMe();

        $camps = $this->getCampRepository()->findCampsByUser($me);

        $groupMemberships = $this->getGroupMembershipRepository()->findByUser($me);

        $friendships = $this->getUserRelationshipRepository()->findFriends($me);
        $friendshipRequests = $this->getUserRelationshipRepository()->findRequests($me);

        return array(
            'camps' => $camps,
            'news' => array(),
            'groupMemberships' => $groupMemberships,
            'friendships' => $friendships,
            'friendshipRequests' => $friendshipRequests
        );
    }

}
