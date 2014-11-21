<?php

namespace EcampCore\Render;

use EcampCore\DI\DependencyLocator;
use EcampCore\Entity\Event;
use EcampCore\Entity\Medium;
use EcampCore\Entity\EventPrototype;

use EcampCore\Repository\Provider\MediumRepositoryProvider;
use EcampCore\Repository\Provider\EventTemplateRepositoryProvider;

use Zend\View\Model\ViewModel;

class EventRenderer
    extends DependencyLocator
    implements 	EventTemplateRepositoryProvider
    ,			MediumRepositoryProvider
{

    protected function getPluginRenderer()
    {
        $pluginRenderer = new PluginRenderer();
        $pluginRenderer->setServiceLocator($this->serviceLocator);

        return $pluginRenderer;
    }

    /**
     * @param  \EcampCore\Entity\Event    $event
     * @param  \EcampCore\Entity\Medium   $medium
     * @return \Zend\View\Model\ViewModel
     */
    public function render(Event $event, Medium $medium = null)
    {
        $medium = $medium ?: $this->ecampCore_MediumRepo()->getDefaultMedium();
        $eventTemplate = $this->getEventTemplate($event->getPrototype(), $medium);

        $view = new ViewModel();
        $view->setTemplate($eventTemplate->getFilename());
        $view->setVariable('event', $event);
        $view->setVariable('eventPrototype', $event->getPrototype());
        $view->setVariable('eventTemplate', $eventTemplate);

        $pluginInstances = array();

        foreach ($event->getPluginInstances() as $pluginInstance) {
            $pluginPrototypeId = $pluginInstance->getPluginPrototype()->getId();
            $pluginInstances[$pluginPrototypeId] = $pluginInstance;
        }

        foreach ($eventTemplate->getPluginPositions() as $pluginPos) {
            $pluginPrototypeId = $pluginPos->getPluginPrototype()->getId();
            $pluginInstance = $pluginInstances[$pluginPrototypeId];

            $pluginView = $this->getPluginRenderer()->render($pluginInstance, $medium);
            $view->setVariable($pluginPos->getContainer(), $pluginView);
            $view->addChild($pluginView, $pluginPos->getContainer());
        }

        return $view;
    }

    /**
     * @param  EventPrototype                 $eventPrototype
     * @param  Medium                         $medium
     * @return \EcampCore\Entity\EventTemplate
     */
    private function getEventTemplate(EventPrototype $eventPrototype, Medium $medium)
    {
        return
            $this->ecampCore_EventTemplateRepo()->findOneBy(array(
                'medium' => $medium,
                'eventPrototype' => $eventPrototype
            ));
    }

}
