<?php

namespace EcampCore\Controller;

use EcampCore\Entity\Medium;

/**
 * @method \EcampCore\Service\UserService getUserService()
 * @method \EcampCore\Service\CampService getCampService()
 * @method \EcampCore\Service\GroupService getGroupService()
 */
abstract class AbstractBaseController extends \EcampLib\Controller\AbstractBaseController
{
    /**
     * @return \EcampCore\Repository\UserRepository
     */
    protected function getUserRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\User');
    }

    /**
     * @return \EcampCore\Repository\CampRepository
     */
    protected function getCampRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Camp');
    }

    /**
     * @return \EcampCore\Repository\GroupRepository
     */
    protected function getGroupRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Group');
    }

    private $repoPattern = "/^get(\w+)Repository$/";
    private $servicePattern = "/^get(\w+)Service$/";

    public function __call($method, $params)
    {
        $matches = null;

        if (preg_match($this->repoPattern, $method, $matches)) {
            $name = 'EcampCore\Repository\\' . $matches[1];
            if ($this->getServiceLocator()->has($name)) {
                return $this->getServiceLocator()->get($name);
            }
        }

        if (preg_match($this->servicePattern, $method, $matches)) {
            $name = 'EcampCore\Service\\' . $matches[1];
            if ($this->getServiceLocator()->has($name)) {
                return $this->getServiceLocator()->get($name);
            }
        }

        return parent::__call($method, $params);
    }

    /**
     * @return \EcampCore\Service\UserService
     */
//    protected function getUserService()
//    {
//        return $this->getServiceLocator()->get('EcampCore\Service\User');
//    }

    /**
     * @return \EcampCore\Service\CampService
     */
//    protected function getCampService()
//    {
//        return $this->getServiceLocator()->get('EcampCore\Service\Camp');
//    }

    /**
     * @return \EcampCore\Service\GroupService
     */
//    protected function getGroupService()
//    {
//        return $this->getServiceLocator()->get('EcampCore\Service\Group');
//    }

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
     * @param string $qry
     * @return \EcampCore\Entity\User
     */
    protected function getQueryUser($qry = 'user')
    {
        $userId = $this->params()->fromQuery($qry);

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
     * @param string $qry
     * @return \EcampCore\Entity\User
     */
    protected function getQueryGroup($qry = 'group')
    {
        $groupId = $this->params()->fromQuery($qry);

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

    /**
     * @param string $qry
     * @return \EcampCore\Entity\Camp
     */
    protected function getQueryCamp($qry = 'camp')
    {
        $campId = $this->params()->fromQuery($qry);

        return $this->getCampService()->Get($campId);
    }

    /**
     * @return \EcampCore\Entity\Medium
     */
    protected function getWebMedium()
    {
        $mediumRepository = $this->getServiceLocator()->get('EcampCore\Repository\Medium');

        return $mediumRepository->find(Medium::MEDIUM_WEB);
    }

    /**
     * @return \EcampCore\Entity\Medium
     */
    protected function getPrintMedium()
    {
        $mediumRepository = $this->getServiceLocator()->get('EcampCore\Repository\Medium');

        return $mediumRepository->find(Medium::MEDIUM_PRINT);
    }
}
