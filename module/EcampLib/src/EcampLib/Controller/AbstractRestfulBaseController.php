<?php

namespace EcampLib\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

abstract class AbstractRestfulBaseController extends AbstractRestfulController
{

    /**
     * @return \EcampCore\Service\UserService
     */
    private function getUserService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\User');
    }

    /**
     * @return \EcampCore\Service\CampService
     */
    private function getCampService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\Camp');
    }

    /**
     * @return \EcampCore\Service\GroupService
     */
    private function getGroupService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\Group');
    }

    protected function createCriteriaArray(array $criteria)
    {
        return array_merge(
            array(
                'offset'	=> $this->params()->fromQuery('offset'),
                'limit'		=> $this->params()->fromQuery('limit'),
            ),
            $criteria
        );
    }

    /**
     * @return \EcampCore\Entity\User
     */
    protected function getMe()
    {
        return $this->getUserService()->Get();
    }

    /**
     * @return \EcampCore\Entity\User
     */
    protected function getRouteUser()
    {
        $userId = $this->params('user');

        return $this->getUserService()->Get($userId);
    }

    /**
     * @return \EcampCore\Entity\Group
     */
    protected function getRouteGroup()
    {
        $groupId = $this->params('group');

        return $this->getGroupService()->Get($groupId);
    }

    /**
     * @return \EcampCore\Entity\Camp
     */
    protected function getRouteCamp()
    {
        $campId = $this->params('camp');

        return $this->getCampService()->Get($campId);
    }

}
