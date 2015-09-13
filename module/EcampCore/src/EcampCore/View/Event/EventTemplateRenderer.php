<?php

namespace EcampCore\View\Event;

use EcampCore\Entity\Event;
use EcampCore\Entity\EventInstance;
use EcampCore\Entity\EventTemplate;
use EcampCore\Plugin\StrategyProvider;
use Zend\View\Model\ViewModel;

class EventTemplateRenderer
{
    /**
     * @var StrategyProvider
     */
    private $strategyProvider;

    /**
     * @var \EcampCore\Entity\EventTemplate
     */
    private $eventTemplate;

    /**
     * @var array
     */
    private $eventTemplateContainerRenderers;

    public function __construct(
        StrategyProvider $strategyProvider,
        EventTemplate $eventTemplate
    ) {
        $this->strategyProvider = $strategyProvider;
        $this->eventTemplate = $eventTemplate;
        $this->eventTemplateContainerRenderers = array();
    }

    /**
     * @return \EcampCore\Entity\EventTemplate
     */
    public function getEventTemplate()
    {
        return $this->eventTemplate;
    }

    /**
     * @return \EcampCore\Entity\EventType
     */
    public function getEventType()
    {
        return $this->eventTemplate->getEventType();
    }

    /**
     * @return \EcampCore\Entity\Medium
     */
    public function getMedium()
    {
        return $this->eventTemplate->getMedium();
    }

    public function buildRendererTree()
    {
        $containers = $this->eventTemplate->getEventTemplateContainers();

        foreach ($containers as $container) {
            /* @var $container \EcampCore\Entity\EventTemplateContainer */
            $eventTemplateContainerRenderer = new EventTemplateContainerRenderer($this->strategyProvider, $container);
            $eventTemplateContainerRenderer->setEventTemplateRenderer($this);

            $this->eventTemplateContainerRenderers[] = $eventTemplateContainerRenderer;
        }
    }

    /**
     * @param  Event         $event
     * @param  EventInstance $eventInstance
     * @return ViewModel
     * @throws \Exception
     */
    public function render(Event $event, EventInstance $eventInstance = null)
    {
        if ($event->getEventType() != $this->getEventType()) {
            throw new \Exception("EventTemplateRenderer can not render the given Event");
        }

        $eventCategory = $event->getEventCategory();
        $eventType = $eventCategory->getEventType();

        $viewModel = new ViewModel();
        $viewModel->setTemplate($this->eventTemplate->getFilename());
        $viewModel->setVariable('event', $event);
        $viewModel->setVariable('eventType', $eventType);
        $viewModel->setVariable('eventCategory', $eventCategory);
        $viewModel->setVariable('eventTemplate', $this->eventTemplate);
        $viewModel->setVariable('eventInstance', $eventInstance);

        foreach ($this->eventTemplateContainerRenderers as $eventTemplateContainerRenderer) {
            /* @var $eventTemplateContainerRenderer \EcampCore\View\Event\EventTemplateContainerRenderer */
            $containerName = $eventTemplateContainerRenderer->getContainerName();
            $viewModel->setVariable($containerName, $eventTemplateContainerRenderer->render($event));
        }

        return $viewModel;
    }

}
