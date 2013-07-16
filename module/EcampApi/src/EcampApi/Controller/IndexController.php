<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\UserSerializer;
use EcampApi\Serializer\CampSerializer;

use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;
use EcampLib\Acl\Exception\AuthenticationRequiredException;

class IndexController extends AbstractRestfulBaseController
{

    /**
     * @return EcampCore\Acl\ContextProvider
     */
    private function contextProvider()
    {
        return $this->getServiceLocator()->get('ecamp.acl.contextprovider');
    }

    /**
     * @return \EcampCore\Repository\CampRepository
     */
    private function getCampRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Camp');
    }

    /**
     * @return \EcampCore\Service\RelationshipService
     */
    private function getRelationshipService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\Relationship');
    }

    public function getList()
    {
        throw new AuthenticationRequiredException();

        $userSerializer = new UserSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        $me = $this->getMe();

        if (isset($me)) {
            return new JsonModel(array(
                'me' => ($me != null) ? $userSerializer->getReference($me) : null,
                'camps' => ($me != null) ? $me->campCollaboration()->getCamps() : null,
                'friends' => ($me != null) ? $me->userRelationship()->getFriends() : null
            ));
        } else {
            return new JsonModel(array(
                'login' => $this->url()->fromRoute('api/default', array('controller' => 'login'))
            ));
        }
    }

    public function get($id)
    {
        $camp = $this->getCampRepository()->find($id);

        $campSerializer = new CampSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($campSerializer($camp));
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
