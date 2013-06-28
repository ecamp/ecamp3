<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\EventInstanceSerializer;
use EcampCore\Repository\Provider\EventInstanceRepositoryProvider;
use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class EventInstancesController extends AbstractRestfulBaseController
    implements EventInstanceRepositoryProvider
{

    public function getList()
    {
        $eventInstances = $this->ecampCore_EventInstanceRepo()->findAll();

        $eventInstanceSerializer = new EventInstanceSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($eventInstanceSerializer($eventInstances));
    }

    public function get($id)
    {
        $eventInstance = $this->ecampCore_EventInstanceRepo()->find($id);

        $eventInstanceSerializer = new EventInstanceSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($eventInstanceSerializer($eventInstance));
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
