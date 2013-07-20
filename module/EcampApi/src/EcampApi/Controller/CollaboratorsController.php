<?php

namespace EcampApi\Controller;

use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;
use EcampApi\Serializer\CollaboratorSerializer;

class CollaboratorsController extends AbstractRestfulBaseController
{
    /**
     * @return \EcampCore\Repository\CampCollaborationRepository
     */
    private function getCampCollaborationRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\CampCollaboration');
    }

    public function getList()
    {
        $criteria = $this->createCriteriaArray(array(
            'camp' 	=> $this->params('camp') ?: $this->params()->fromQuery('camp'),
            'user' 	=> $this->params('user') ?: $this->params()->fromQuery('user')
        ));

        $collaborators = $this->getCampCollaborationRepository()->findForApi($criteria);

        $collaboratorSerializer = new CollaboratorSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($collaboratorSerializer($collaborators));
    }

    public function get($id)
    {
        $collaborator = $this->getCampCollaborationRepository()->find($id);

        $collaboratorSerializer = new CollaboratorSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($collaboratorSerializer($collaborator));
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
