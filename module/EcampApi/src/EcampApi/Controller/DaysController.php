<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\DaySerializer;
use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class DaysController extends AbstractRestfulBaseController
{

    /**
     * @return \EcampCore\Repository\DayRepository
     */
    private function getDayRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Day');
    }

    public function getList()
    {
        $criteria = $this->createCriteriaArray(array());

        $days = $this->getDayRepository()->findForApi($criteria);

        $daySerializer = new DaySerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($daySerializer($days));
    }

    public function get($id)
    {
        $day = $this->getDayRepository()->find($id);

        $daySerializer = new DaySerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($daySerializer($day));
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
