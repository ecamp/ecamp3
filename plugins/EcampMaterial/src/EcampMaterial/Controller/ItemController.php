<?php

namespace EcampMaterial\Controller;

use EcampCore\Service\Params\Params;
use EcampCore\Plugin\PluginBaseController;

use Zend\View\Model\ViewModel;

class ItemController extends PluginBaseController
{

    public function getItemRepo()
    {
        return $this->getServiceLocator()->get('EcampMaterial\Repository\Item');
    }

    public function addAction()
    {
        $sectionId = $this->params()->fromRoute('id');
        $section = $this->getSectionRepo()->find($sectionId);

        $viewModel = new ViewModel();
        $viewModel->setVariable('section', $section);
        $viewModel->setTemplate('ecamp-material/section');

        return $viewModel;
    }

}
