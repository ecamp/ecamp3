<?php

namespace EcampMaterial\Controller;

use EcampCore\Controller\AbstractEventPluginController;

use Zend\View\Model\ViewModel;

class ItemController extends AbstractEventPluginController
{

    /**
     * @return \EcampMaterial\Service\ItemService
     */
    private function getSectionService()
    {
        return $this->getServiceLocator()->get('EcampMaterial\Service\Item');
    }

    public function createAction()
    {
        $eventPlugin = $this->getRouteEventPlugin();
        $data = $this->params()->fromPost();

        $item = $this->getSectionService()->create($eventPlugin, $data);

        $viewModel = new ViewModel();
        $viewModel->setVariable('item', $item);
        $viewModel->setVariable('eventPlugin', $eventPlugin);
        $viewModel->setTemplate('ecamp-material/item');

        return $viewModel;
    }

}
