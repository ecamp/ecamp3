<?php

namespace EcampApi\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class AuthController extends AbstractRestfulController
{

    /**
     * @return \EcampApi\Service\ApiKeyService
     */
    public function getApiKeyService()
    {
        return $this->getServiceLocator()->get('EcampApi\Service\ApiKey');
    }

    public function createKeyAction()
    {
        $appName = $this->params()->fromPost('appName');
        $deviceName = $this->params()->fromPost('deviceName');

        $apiKey = $this->getApiKeyService()->CreateApiKey($appName, $deviceName);

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent($apiKey);

        return $response;
    }

    public function loginAction()
    {
        $user = $this->params()->fromPost('user');
        $apiKey = $this->params()->fromPost('apiKey');
        $appName = $this->params()->fromPost('appName');
        $deviceName = $this->params()->fromPost('deviceName');

        $result = $this->getApiKeyService()->Login($user, $apiKey, $appName, $deviceName);

        $response = $this->getResponse();

        if ($result->isValid()) {
            $response->setStatusCode(200);
            $response->sendHeaders();

            return $response;

        } else {
            $response->setStatusCode(401);
            $response->sendHeaders();

            return new JsonModel($result->getMessages());
        }
    }

    public function logoutAction()
    {
        $this->getApiKeyService()->Logout();

        $response = $this->getResponse();
        $response->setStatusCode(200);

        return $response;
    }

}
