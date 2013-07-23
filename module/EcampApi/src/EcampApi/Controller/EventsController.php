<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\EventSerializer;
use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class EventsController extends AbstractRestfulBaseController
{

    /**
     * @return \EcampCore\Repository\EventRepository
     */
    private function getEventRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Event');
    }

    public function getList()
    {
        $criteria = $this->createCriteriaArray(array(
            'camp'		=> $this->params('camp') ?: $this->params()->fromQuery('camp')
        ));

        $events = $this->getEventRepository()->findForApi($criteria);

        $eventSerializer = new EventSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($eventSerializer($events));
    }

    public function get($id)
    {
        $event = $this->getEventRepository()->find($id);

        $eventSerializer = new EventSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($eventSerializer($event));
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
