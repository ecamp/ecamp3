<?php

namespace EcampCore\View\Event;

use EcampCore\Entity\Event;
use EcampCore\Entity\EventTemplate;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventTemplateRenderer
{
    /**
     * @var \EcampCore\Entity\EventTemplate
     */
    private $eventTemplate;

    /**
     * @var array
     */
    private $eventTemplateContainerRenderers;

    public function __construct(EventTemplate $eventTemplate)
    {
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

    public function buildRendererTree(ServiceLocatorInterface $serviceLocator)
    {
        $containers = $this->eventTemplate->getEventTemplateContainers();

        foreach ($containers as $container) {
            /* @var $container \EcampCore\Entity\EventTemplateContainer */
            $eventTemplateContainerRenderer = new EventTemplateContainerRenderer($container);
            $eventTemplateContainerRenderer->setEventTemplateRenderer($this);
            $eventTemplateContainerRenderer->buildRendererTree($serviceLocator);

            $this->eventTemplateContainerRenderers[] = $eventTemplateContainerRenderer;
        }
    }

    /**
     * @param  Event                      $event
     * @return \Zend\View\Model\ViewModel
     */
    public function render(Event $event)
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

        foreach ($this->eventTemplateContainerRenderers as $eventTemplateContainerRenderer) {
            /* @var $eventTemplateContainerRenderer \EcampCore\View\Event\EventTemplateContainerRenderer */
            $containerName = $eventTemplateContainerRenderer->getContainerName();
            $viewModel->setVariable($containerName, $eventTemplateContainerRenderer->render($event));
        }

        return $viewModel;
    }

}
