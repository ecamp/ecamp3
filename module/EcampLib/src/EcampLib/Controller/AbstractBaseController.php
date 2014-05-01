<?php

namespace EcampLib\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractBaseController extends AbstractActionController
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

    /**
     * @return \Zend\Form\FormElementManager
     */
    protected function getFormElementManager()
    {
        return $this->getServiceLocator()->get('FormElementManager');
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

    /**
     * @param  integer  $statusCode
     * @return Response
     */
    protected function emptyResponse($statusCode = Response::STATUS_CODE_200)
    {
        /* @var $response Response */
        $response = $this->getResponse();
        $response->setStatusCode($statusCode);

        return $response;
    }

}
