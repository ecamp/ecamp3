<?php

namespace EcampCore\View\Event;

use EcampCore\Entity\Event;
use EcampCore\Entity\EventTemplateContainer;
use EcampCore\Plugin\StrategyProvider;
use Zend\View\Model\ViewModel;

class EventTemplateContainerRenderer
{
    /**
     * @var \EcampCore\Entity\EventTemplateContainer
     */
    private $eventTemplateContainer;

    /**
     * @var \EcampCore\Plugin\StrategyProvider
     */
    private $strategyProvider;

    /**
     * @var \EcampCore\View\Event\EventTemplateRenderer
     */
    private $eventTemplateRenderer;

    /**
     * @var array
     */
    private $pluginPositionRenderers;

    public function __construct(
        StrategyProvider $strategyProvider,
        EventTemplateContainer $eventTemplateContainer
    ) {
        $this->strategyProvider = $strategyProvider;
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
        $viewModel->setTemplate($eventTemplateContainer->getFilename());
        $viewModel->setVariable('plugin', $plugin);
        $viewModel->setVariable('event', $event);
        $viewModel->setVariable('camp', $event->getCamp());
        $viewModel->setVariable('eventTypePlugin', $eventTypePlugin);
        $viewModel->setVariable('eventTemplateContainer', $eventTemplateContainer);

        $pluginStrategy = $this->strategyProvider->Get($plugin);

        $childViewModels = array();

        foreach ($eventPlugins as $eventPlugin) {

            $itemViewModel = new ViewModel();
            $itemViewModel->setTemplate($eventTemplateContainer->getFilename().'.item');
            $itemViewModel->setVariable('eventPlugin', $eventPlugin);
            $itemViewModel->setVariable('contentViewModel', $pluginStrategy->render($eventPlugin, $medium));

            $childViewModels[] = $itemViewModel;
        }

        $viewModel->setVariable('childViewModels', $childViewModels);

        return $viewModel;

    }
}
