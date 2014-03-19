<?php

namespace EcampStoryboard\Controller;

use Zend\View\Model\ViewModel;
use EcampCore\Controller\AbstractEventPluginController;
use EcampStoryboard\Entity\Section;
use Zend\Http\PhpEnvironment\Response;

class SectionsController extends AbstractEventPluginController
{

    /**
     * @return \EcampStoryboard\Repository\SectionRepository
     */
    private function getSectionRepo()
    {
        return $this->getServiceLocator()->get('EcampStoryboard\Repository\Section');
    }

    /**
     * @return \EcampStoryboard\Service\SectionService
     */
    private function getSectionService()
    {
        return $this->getServiceLocator()->get('EcampStoryboard\Service\Section');
    }

    public function createAction()
    {
        $eventPlugin = $this->getRouteEventPlugin();
        $seciton = $this->getSectionService()->create($eventPlugin);

        $viewModel = new ViewModel();
        $viewModel->setVariable('section', $seciton);
        $viewModel->setVariable('eventPlugin', $seciton->getEventPlugin());
        $viewModel->setTemplate('ecamp-storyboard/section');

        return $viewModel;
    }

    public function saveAction()
    {
        $sectionId = $this->params()->fromRoute('id');
        $section = $this->getSectionRepo()->find($sectionId);

        $data = $this->params()->fromPost();
        $data['duration_in_minutes'] = 60 * ($data['duration_hour'] ?: 0) + ($data['duration_minute'] ?: 0);
        $data['text'] = $data['section_text'];
        $data['info'] = $data['section_info'];

        $this->getSectionService()->update($section, $data);

        $viewModel = new ViewModel();
        $viewModel->setVariable('section', $section);
        $viewModel->setVariable('eventPlugin', $section->getEventPlugin());
        $viewModel->setTemplate('ecamp-storyboard/section');

        return $viewModel;
    }

    public function deleteAction()
    {
        $sectionId = $this->params()->fromRoute('id');
        $section = $this->getSectionRepo()->find($sectionId);

        try {
            $this->getSectionService()->delete($section);

            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_200);

            return $response;

        } catch (\Exception $ex) {
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_500);
            $response->setContent($ex->getMessage());

            return $response;
        }

    }

    public function moveAction()
    {
        $direction = strtolower($this->params()->fromQuery('direction'));
        $sectionId = $this->params()->fromRoute('id');
        $section = $this->getSectionRepo()->find($sectionId);

        try {
            if (!in_array($direction, array('up', 'down'))) {
                throw new \Exception("Direction not defined");
            }

            if ($direction == 'up') {
                $this->getSectionService()->moveUp($section);
            } else {
                $this->getSectionService()->moveDown($section);
            }

            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_200);

            return $response;

        } catch (\Exception $ex) {
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_500);
            $response->setContent($ex->getMessage());

            return $response;
        }
    }
}
