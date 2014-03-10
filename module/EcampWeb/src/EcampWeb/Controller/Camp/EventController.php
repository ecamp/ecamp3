<?php

namespace EcampWeb\Controller\Camp;

use Zend\View\Model\ViewModel;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Mvc\Controller\Plugin\Params;
use EcampCore\View\Event\EventTemplateRenderer;

class EventController extends BaseController
{
    /**
     * @var \EcampCore\Entity\Event
     */
    private $eventEntity = null;

    /**
     * @return \EcampCore\Entity\Event
     */
    private function getEventEntity()
    {
        return $this->eventEntity;
    }

    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        $eventEntityId = $request->getQuery('eventId');
        $this->eventEntity = $this->getEventRepository()->find($eventEntityId);

        if ($this->eventEntity == null) {
            throw new \Exception("Event not defined.");
        }

        parent::dispatch($request, $response);
    }

    /**
     * @return \EcampCore\Repository\EventRepository
     */
    private function getEventRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Event');
    }

    /**
     * @return \EcampCore\Repository\DayRepository
     */
    private function getDayRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Day');
    }

    /**
     * @return \EcampCore\Repository\EventTemplateRepository
     */
    private function getEventTemplateRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\EventTemplate');
    }

    /**
     * @return \EcampCore\Repository\EventPluginRepository
     */
    private function getEventPluginRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\EventPlugin');
    }

    /**
     * @return \EcampCore\Service\EventRespService
     */
    private function getEventRespService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\EventResp');
    }

    public function indexAction()
    {
        $medium = $this->getWebMedium();
        $event = $this->getEventEntity();

        $eventTemplate = $this->getEventTemplateRepository()->findTemplate($event, $medium);
        $eventTemplateRenderer = new EventTemplateRenderer($eventTemplate);
        $eventTemplateRenderer->buildRendererTree($this->getServiceLocator());

        $viewModel = $eventTemplateRenderer->render($event);

        return $viewModel;
    }

    public function pluginAction()
    {
        $medium = $this->getWebMedium();

        $eventPluginId = $this->getRequest()->getQuery('eventPluginId');
        $eventPlugin = $this->getEventPluginRepository()->find($eventPluginId);

        /* @var $eventPlugin \EcampCore\Entity\EventPlugin */
        $pluginStrategyClass = $eventPlugin->getPluginStrategyClass();

        /* @var $pluginStrategyInstanceFactory \EcampCore\Plugin\AbstractStrategyFactory */
        $pluginStrategyInstanceFactory = $this->getServiceLocator()->get($pluginStrategyClass);

        /* @var $pluginStrategyInstance \EcampCore\Plugin\AbstractStrategy */
        $pluginStrategyInstance = $pluginStrategyInstanceFactory->createStrategy($eventPlugin, $medium);

        return $pluginStrategyInstance->render();
    }

    public function setRespAction()
    {
        $respUserIds = $this->params()->fromPost('resp_user_ids');
        $event = $this->getEventEntity();

        $users = array();
        if (is_array($respUserIds)) {
            foreach ($respUserIds as $respUserId) {
                $users[$respUserId] = $this->getUserRepository()->find($respUserId);
            }
        }

        $this->getEventRespService()->SetResponsableUsers($event, $users);

        $viewModel = new ViewModel(array(
            'camp' => $event->getCamp(),
            'event' => $event,
        ));
        $viewModel->setTemplate('ecamp-web/event-templates/base/event-resp-select');

        return $viewModel;

    }

}
