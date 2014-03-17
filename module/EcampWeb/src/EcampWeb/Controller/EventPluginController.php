<?php

namespace EcampWeb\Controller;

use EcampCore\Entity\Medium;
use Zend\Http\PhpEnvironment\Response;
use EcampCore\Controller\AbstractEventPluginController;
use Zend\View\Model\ViewModel;

class EventPluginController extends AbstractEventPluginController
{
    /**
     * @return \EcampCore\Repository\EventTemplateContainerRepository
     */
    protected function getEventTemplateContainerRepository()
    {
        return $this->serviceLocator->get('EcampCore\Repository\EventTemplateContainer');
    }

    /**
     * @return \EcampCore\Entity\Medium
     */
    protected function getWebMedium()
    {
        return $this->getMediumRepository()->find(Medium::MEDIUM_WEB);
    }

    public function createAction()
    {
        try {
            $event = $this->getRouteEvent();
            $eventType = $event->getEventCategory()->getEventType();
            $plugin = $this->getRoutePlugin();
            $webMedium = $this->getWebMedium();

            $pluginStrategy = $this->getPluginStrategyInstance($plugin);
            $eventPlugin = $pluginStrategy->create($event, $plugin);

            $eventTemplateContainer =
                $this->getEventTemplateContainerRepository()
                    ->findTemplateContainer($event, $plugin, $webMedium);

            $viewModel = new ViewModel();
            $viewModel->setTemplate($eventTemplateContainer->getFilename().'.item');
            $viewModel->setVariable('eventPlugin', $eventPlugin);
            $viewModel->setVariable('contentViewModel', $pluginStrategy->render($eventPlugin, $webMedium));

            return $viewModel;

        } catch (\Exception $ex) {
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_500);
            $response->setContent($ex->getMessage());

            return $response;
        }
    }

    public function getAction()
    {
        try {
            $eventPlugin = $this->getRouteEventPlugin();
            $pluginStrategy = $this->getPluginStrategyInstance($eventPlugin->getPlugin());

            $event = $eventPlugin->getEvent();
            $plugin = $eventPlugin->getPlugin();
            $webMedium = $this->getWebMedium();

            $eventTemplateContainer =
                $this->getEventTemplateContainerRepository()
                    ->findTemplateContainer($event, $plugin, $webMedium);

            $itemViewModel = new ViewModel();
            $itemViewModel->setTemplate($eventTemplateContainer->getFilename().'.item');
            $itemViewModel->setVariable('eventPlugin', $eventPlugin);
            $itemViewModel->setVariable('contentViewModel', $pluginStrategy->render($eventPlugin, $webMedium));

            return $itemViewModel;

        } catch (\Exception $ex) {
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_500);
            $response->setContent($ex->getMessage());

            return $response;
        }
    }

}
