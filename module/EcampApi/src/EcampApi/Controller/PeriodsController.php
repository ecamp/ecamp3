<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\PeriodSerializer;
use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class PeriodsController extends AbstractRestfulBaseController
{

    /**
     * @return \EcampCore\Repository\PeriodRepository
     */
    private function getPeriodRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Period');
    }

    public function getList()
    {
        $criteria = $this->createCriteriaArray(array());

        $periods = $this->getPeriodRepository()->findForApi($criteria);

        $periodSerializer = new PeriodSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($periodSerializer($periods));
    }

    public function get($id)
    {
        $period = $this->getPeriodRepository()->find($id);

        $periodSerializer = new PeriodSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($periodSerializer($period));
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
