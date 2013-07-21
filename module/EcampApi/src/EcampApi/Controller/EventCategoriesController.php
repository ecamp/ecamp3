<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\EventCategorySerializer;
use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class EventCategoriesController extends AbstractRestfulBaseController
{

    /**
     * @return \EcampCore\Repository\EventCategoryRepository
     */
    private function getEventCategoryRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\EventCategory');
    }

    public function getList()
    {
        $criteria = $this->createCriteriaArray(array(
            'camp'	=> $this->params('camp')  ?: $this->params()->fromQuery('camp'),
        ));

        $eventCategories = $this->getEventCategoryRepository()->findForApi($criteria);

        $eventCategorySerializer = new EventCategorySerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($eventCategorySerializer($eventCategories));
    }

    public function get($id)
    {
        $eventCategory = $this->getEventCategoryRepository()->find($id);

        $eventCategorySerializer = new EventCategorySerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($eventCategorySerializer($eventCategory));
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
