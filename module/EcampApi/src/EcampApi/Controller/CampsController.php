<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\CampSerializer;
use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class CampsController extends AbstractRestfulBaseController
{

    /**
     * @return \EcampCore\Repository\CampRepository
     */
    private function getCampRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Camp');
    }

    public function getList()
    {
        $criteria = $this->createCriteriaArray(array(
            'user'			=> $this->params('user'),
            'owner' 		=> $this->params()->fromQuery('owner'),
            'owning_only'	=> $this->params()->fromQuery('owning_only'),
            'group'			=> $this->params()->fromQuery('group'),
            'creator'		=> $this->params()->fromQuery('creator'),
            'collaborator' 	=> $this->params()->fromQuery('collaborator'),
            'search'		=> $this->params()->fromQuery('search'),
            'past'			=> $this->params()->fromQuery('past')
        ));

        $camps = $this->getCampRepository()->findForApi($criteria);

        $campSerializer = new CampSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($campSerializer($camps));
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
