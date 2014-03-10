<?php

namespace EcampStoryboard\Controller;

use EcampCore\Service\Params\Params;
use EcampCore\Plugin\PluginBaseController;

use Zend\View\Model\ViewModel;

class SectionsController extends PluginBaseController
{

    public function getSectionRepo()
    {
        return $this->getServiceLocator()->get('EcampStoryboard\Repository\Section');
    }

    public function saveAction()
    {
        $sectionId = $this->params()->fromRoute('id');
        $section = $this->getSectionRepo()->find($sectionId);

        $viewModel = new ViewModel();
        $viewModel->setVariable('section', $section);
        $viewModel->setTemplate('ecamp-storyboard/section');

        return $viewModel;
    }

}
