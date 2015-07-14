<?php

namespace EcampWeb\Controller\Camp;

use EcampCore\Entity\Event;
use EcampCore\Plugin\StrategyProvider;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Mvc\Controller\Plugin\Params;
use EcampCore\View\Event\EventTemplateRenderer;
use EcampCore\Entity\Medium;

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
     * @return StrategyProvider
     */
    private function getStrategyProvider()
    {
        return $this->getServiceLocator()->get('EcampCore\Plugin\StrategyProvider');
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

    private function getEventViewModel(Medium $medium)
    {
        $event = $this->getEventEntity();

        $strategyProvider = $this->getStrategyProvider();
        $eventTemplate = $this->getEventTemplateRepository()->findTemplate($event, $medium);
        $eventTemplateRenderer = new EventTemplateRenderer($strategyProvider, $eventTemplate);
        $eventTemplateRenderer->buildRendererTree($this->getServiceLocator());

        return $eventTemplateRenderer->render($event);
    }

    public function indexAction()
    {
        $medium = $this->getWebMedium();

        return $this->getEventViewModel($medium);
    }

    public function printJobCreateAction()
    {
        $event = $this->getEventEntity();

        $token = \Resque::enqueue('ecamp3', 'EcampCore\Job\EventPrinter', array(
            'printSingleEvent',
            'eventId' => $event->getId()
            ), true);

        /*
        $token2 = \Resque::enqueue('ecamp3', 'Zf2Cli', array(
                'command' => "job dummy test2"
        ), true);*/

        die("$token");
    }

    public function printJobGenerateAction()
    {
        $medium = $this->getPrintMedium();

        return $this->getEventViewModel($medium);
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

        $this->getEventRespService()->setResponsableUsers($event, $users);

        $viewModel = new ViewModel(array(
            'camp' => $event->getCamp(),
            'event' => $event,
        ));
        $viewModel->setTemplate('ecamp-web/event-templates/base/event-resp-select');

        return $viewModel;

    }

}
