<?php

namespace EcampCore\Resource;

use \EcampLib\Resource\BaseResourceListener as LibResourceListener;

abstract class BaseResourceListener extends LibResourceListener
{
    /**
     * @return \EcampCore\Service\UserService
     */
    protected function getUserService()
    {
        return $this->getService('EcampCore\Service\User');
    }

    /**
     * @return \EcampCore\Entity\User
     */
    protected function getIdentifiedUser()
    {
        return $this->getUserService()->Get();
    }

}
