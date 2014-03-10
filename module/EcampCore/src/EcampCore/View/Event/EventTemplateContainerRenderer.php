<?php

namespace EcampCore\View\Event;

use EcampCore\Entity\Event;
use EcampCore\Entity\EventTemplateContainer;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventTemplateContainerRenderer
{
    /**
     * @var \EcampCore\Entity\EventTemplateContainer
     */
    private $eventTemplateContainer;

    /**
     * @var \EcampCore\Plugin\AbstractStrategyFactory
     */
    private $pluginStrategyInstanceFactory;

    /**
     * @var \EcampCore\View\Event\EventTemplateRenderer
     */
    private $eventTemplateRenderer;

    /**
     * @var array
     */
    private $pluginPositionRenderers;

    public function __construct(EventTemplateContainer $eventTemplateContainer)
    {
        $this->eventTemplateContainer = $eventTemplateContainer;
        $this->pluginPositionRenderers = array();
    }

    /**
     * @return \EcampCore\Entity\EventTemplateContainer
     */
    public function getEventTemplateContainer()
    {
        return $this->eventTemplateContainer;
    }

    /**
     * @return \EcampCore\Entity\EventTemplate
     */
    public function getEventTemplate()
    {
        return $this->getEventTemplateContainer()->getEventTemplate();
    }

    public function getContainerName()
    {
        return $this->eventTemplateContainer->getContainerName();
    }

    public function setEventTemplateRenderer(EventTemplateRenderer $eventTemplateRenderer)
    {
        $this->eventTemplateRenderer = $eventTemplateRenderer;
    }

    public function getEventTemplateRenderer()
    {
        return $this->eventTemplateRenderer;
    }

    public function buildRendererTree(ServiceLocatorInterface $serviceLocator)
    {
        $eventTypePlugin = $this->eventTemplateContainer->getEventTypePlugin();

        $pluginStrategyClass = $eventTypePlugin->getPlugin()->getStrategyClass();
        $this->pluginStrategyInstanceFactory = $serviceLocator->get($pluginStrategyClass);

        /*
        $pluginPositions = $this->eventTemplateContainer-> getPluginPositions();

        foreach ($pluginPositions as $pluginPosition) {
            / * @var $pluginPosition \EcampCore\Entity\PluginPosition * /
            $pluginPositionRenderer = new PluginPositionRenderer($pluginPosition);
            $pluginPositionRenderer->setEventTemplateContainerRenderer($this);
            $pluginPositionRenderer->buildRendererTree($serviceLocator);

            $this->pluginPositionRenderers[] = $pluginPositionRenderer;
        }
        */
    }

    /**
     * @param  Event                      $event
     * @return \Zend\View\Model\ViewModel
     */
    public function render(Event $event)
    {
        $eventTemplateContainer = $this->eventTemplateContainer;
        $eventTypePlugin = $eventTemplateContainer->getEventTypePlugin();
        $plugin = $eventTypePlugin->getPlugin();
        $medium = $this->getEventTemplate()->getMedium();

        $eventPlugins = $event->getEventPluginsByPlugin($plugin);

        $viewModel = new ViewModel();
        $viewModel->setTemplate($this->eventTemplateContainer->getFilename());
        $viewModel->setCaptureTo($this->eventTemplateContainer->getContainerName());
        $viewModel->setVariable('plugin', $plugin);
        $viewModel->setVariable('event', $event);
        $viewModel->setVariable('camp', $event->getCamp());
        $viewModel->setVariable('eventTypePlugin', $eventTypePlugin);
        $viewModel->setVariable('eventTemplateContainer', $this->eventTemplateContainer);

        $childViewModels = array();

        foreach ($eventPlugins as $eventPlugin) {
            $pluginStrategyInstance = $this->pluginStrategyInstanceFactory->createStrategy($eventPlugin, $medium);
            $childViewModel = $pluginStrategyInstance->render();

            if ($childViewModel->getVariable('title') == null) {
                $childViewModel->setVariable('title', $pluginStrategyInstance->getTitle());
            }

            $childViewModels[] = $childViewModel;
        }

        $viewModel->setVariable('childViewModels', $childViewModels);

        return $viewModel;

    }
}
