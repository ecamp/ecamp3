<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\DaySerializer;
use EcampCore\Repository\Provider\DayRepositoryProvider;
use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class DaysController extends AbstractRestfulBaseController
    implements DayRepositoryProvider
{

    public function getList()
    {
        $days = $this->ecampCore_DayRepo()->findAll();

        $daySerializer = new DaySerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($daySerializer($days));
    }

    public function get($id)
    {
        $day = $this->ecampCore_DayRepo()->find($id);

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
