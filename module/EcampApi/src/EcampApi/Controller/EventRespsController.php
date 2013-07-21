<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\EventRespSerializer;
use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class EventRespsController extends AbstractRestfulBaseController
{

    /**
     * @return \EcampCore\Repository\EventRespRepository
     */
    private function getEventRespRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\EventResp');
    }

    public function getList()
    {
        $criteria = $this->createCriteriaArray(array(
            'event'			=> $this->params('event') ?: $this->params()->fromQuery('event'),
            'user'			=> $this->params('user')  ?: $this->params()->fromQuery('user'),
            'collaboration'	=> $this->params('collaboration') ?: $this->params()->fromQuery('collaboration')
        ));

        $eventResps = $this->getEventRespRepository()->findForApi($criteria);

        $eventRespSerializer = new EventRespSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($eventRespSerializer($eventResps));
    }

    public function get($id)
    {
        $eventResp = $this->getEventRespRepository()->find($id);

        $eventRespSerializer = new EventRespSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($eventRespSerializer($eventResp));
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
