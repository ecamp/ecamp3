<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\CampSerializer;
use EcampCore\Repository\Provider\CampRepositoryProvider;
use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class CampsController extends AbstractRestfulBaseController
{

    public function getList()
    {
    	/* @var $campRepo \EcampCore\Repository\CampRepository */
    	$campRepo = $this->getServiceLocator()->get('EcampCore\Repository\Camp');
        $camps = $campRepo->findAll();

        $campSerializer = new CampSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($campSerializer($camps));
    }

    public function get($id)
    {
        $camp = $this->ecampCore_CampRepo()->find($id);

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
