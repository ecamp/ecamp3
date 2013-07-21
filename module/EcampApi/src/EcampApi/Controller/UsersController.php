<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\UserSerializer;
use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class UsersController extends AbstractRestfulBaseController
{

    /**
     * @return \EcampCore\Repository\UserRepository
     */
    private function getUserRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\User');
    }

    public function getList()
    {
        $criteria = $this->createCriteriaArray(array());

        $users = $this->getUserRepository()->findForApi($criteria);

        $userSerializer = new UserSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($userSerializer($users));
    }

    public function get($id)
    {
        $user = $this->getUserRepository()->find($id);

        $userSerializer = new UserSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($userSerializer($user));
    }

    public function head($id = null)
    {
        $format = $this->params('format');
        die("head." . $format);
    }

    public function create($data)
    {
        $format = $this->params('format');
        die("create." . $format);
    }

    public function update($id, $data)
    {
        $format = $this->params('format');
        die("update." . $format);
    }

    public function delete($id)
    {
        $format = $this->params('format');
        die("delete." . $format);
    }
}
