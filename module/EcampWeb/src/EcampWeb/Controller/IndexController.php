<?php

namespace EcampWeb\Controller;

use EcampCore\Entity\GroupMembership;

class IndexController
    extends BaseController
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

        if ($me != null) {
            $camps = $this->getCampRepository()->findCampsByUser($me);
            $groupMemberships = $this->getGroupMembershipRepository()->findByUser($me);

            $friendships = $this->getUserRelationshipRepository()->findFriends($me);
            $friendshipRequests = $this->getUserRelationshipRepository()->findRequests($me);
        }

        return array(
            'camps' => $camps,
            'news' => array(),
            'groupMemberships' => $groupMemberships,
            'friendships' => $friendships,
            'friendshipRequests' => $friendshipRequests
        );
    }

}
