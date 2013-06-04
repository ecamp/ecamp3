<?php

namespace EcampStoryboard\Controller;

use EcampLib\Controller\AbstractRestfulBaseController;
use EcampCore\Service\Params\Params;
use EcampCore\ServiceUtil\ValidationException;
use EcampStoryboard\Serializer\SectionSerializer;

use Zend\View\Model\JsonModel;

class SectionsController extends AbstractRestfulBaseController
{

    public function getList()
    {
        $sections = $this->ecampStoryboard_SectionRepo()->findAll();

        $sectionSerializer = new SectionSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($sectionSerializer($sections));
    }

    public function get($id)
    {
        $section = $this->ecampStoryboard_SectionRepo()->find($id);

        $sectionSerializer = new SectionSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($sectionSerializer($section));
    }

    public function head($id = null)
    {
        $format = $this->params('format');
        die("head." . $format);
    }

    public function create($data)
    {
        $pluginInstanceId = $this->params('pluginInstanceId');
        $pluginInstance = $this->ecampCore_PluginInstanceRepo()->find($pluginInstanceId);

        $params = Params::FromArray($this->params()->fromPost());
        try {
            $section = $this->ecampStoryboard_SectionService()->create($pluginInstance, $params);

        } catch (ValidationException $e) {
            return new JsonModel(array(
                'error_message' => $e->getMessage()
            ));
        }

        return new JsonModel(array(
            'id' => $section->getId()
        ));
    }

    public function update($id, $data)
    {
        $section = $this->ecampStoryboard_SectionRepo()->find($id);
        $pluginInstanceId = $this->params('pluginInstanceId');
        if ($section->getPluginInstance()->getId() != $this->params('pluginInstanceId')) {
            return new JsonModel(array(
                'error_message' => "Section [$id] does not belong to PluginInstance [$pluginInstanceId]"
            ));
        }

        $params = Params::FromArray($data);
        $this->ecampStoryboard_SectionService()->update($section, $params);

        return new JsonModel(array(
            'id' => $section->getId()
        ));
    }

    public function delete($id)
    {
        $section = $this->ecampStoryboard_SectionRepo()->find($id);
        $pluginInstanceId = $this->params('pluginInstanceId');

        if ($section->getPluginInstance()->getId() != $this->params('pluginInstanceId')) {
            return new JsonModel(array(
                'error_message' => "Section [$id] does not belong to PluginInstance [$pluginInstanceId]"
            ));
        }

        $this->ecampStoryboard_SectionService()->delete($section);

        return new JsonModel();
    }

    public function createAction()
    {
        $data = $this->params()->fromPost();

        return $this->create($data);
    }

    public function deleteAction()
    {
        $id = $this->params('id');

        return $this->delete($id);
    }

    public function moveUpAction()
    {
        $sectionId = $this->params('id');
        $section = $this->ecampStoryboard_SectionRepo()->find($sectionId);

        try {
            $this->ecampStoryboard_SectionService()->moveUp($section);
        } catch (ValidationException $e) {
            return new JsonModel(array(
                'error_message' => $e->getMessage()
            ));
        }

        return new JsonModel();
    }

    public function moveDownAction()
    {
        $sectionId = $this->params('id');
        $section = $this->ecampStoryboard_SectionRepo()->find($sectionId);

        try {
            $this->ecampStoryboard_SectionService()->moveDown($section);
        } catch (ValidationException $e) {
            return new JsonModel(array(
                'error_message' => $e->getMessage()
            ));
        }

        return new JsonModel();
    }

}
