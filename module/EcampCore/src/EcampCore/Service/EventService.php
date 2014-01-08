<?php

namespace EcampCore\Service;

use Core\Plugin\RenderPluginInstance;
use Core\Plugin\RenderContainer;
use Core\Plugin\RenderPluginPrototype;
use Core\Plugin\RenderEvent;

use EcampCore\Entity\Medium;
use EcampCore\Entity\Event;
use EcampCore\Entity\Camp;
use EcampCore\Entity\Plugin;
use EcampCore\Entity\PluginInstance;

use EcampCore\Entity\EventPrototype;
use EcampCore\Entity\PluginPrototype;
use EcampLib\Service\ServiceBase;
use EcampCore\Repository\EventCategoryRepository;
use EcampLib\Validation\ValidationException;
use EcampCore\Validation\EventFieldset;

class EventService
    extends ServiceBase
{
    /**
     * @var \EcampCore\Repository\EventCategoryRepository
     */
    private $eventCategoryRepo;

    public function __construct(
        EventCategoryRepository $eventCategoryRepo
    ){
        $this->eventCategoryRepo = $eventCategoryRepo;
    }

    /**
     * @return CoreApi\Entity\Event | NULL
     */
    public function Get($id)
    {
        if (is_string($id)) {
            $this->repo()->eventRepository()->find($id);
        }

        if ($id instanceof Event) {
            return $id;
        }

        return null;
    }

    public function CreateRenderEvent(Event $event, $medium, $backend = false)
    {
        if (! $medium instanceof Medium) {
            $this->repo()->mediumRepository()->findOneBy(array('name' => $medium));
        }

        $eventPrototype = $event->getPrototype();
        $eventTemplate = $this->repo()->eventTemplateRepository()->findOneBy(
            array('eventPrototype' => $eventPrototype, 'medium' => $medium));

        $renderEvent = new RenderEvent($event, $medium, $eventTemplate, $backend);
        $renderContainers = array();

        $pluginPositions = $eventTemplate->getPluginPositions();
        foreach ($pluginPositions as $pluginPosition) {

            $containerName = $pluginPosition->getContainer();
            if (! array_key_exists($containerName, $renderContainers)) {
                $renderContainers[$containerName] = new RenderContainer($renderEvent, $containerName);
            }
            $renderContainer = $renderContainers[$containerName];
            $pluginPrototype = $pluginPosition->getPluginPrototype();

            $renderPluginPrototype = new RenderPluginPrototype($renderContainer, $pluginPrototype);

            $pluginInstances = $this->repo()->pluginInstanceRepository()->findBy(
                array('event' => $event, 'pluginPrototype' => $pluginPrototype));

            foreach ($pluginInstances as $pluginInstance) {
                new RenderPluginInstance($renderPluginPrototype, $pluginInstance);
            }
        }

        return $renderEvent;
    }

    /**
     * @return bool
     */
    public function Delete($id)
    {
        $event = $this->Get($id);

        foreach ($event->getPluginInstances() as $plugin) {
            $plugin->getStrategyInstance()->remove();
        }

        $this->em->remove($event);

        return true;
    }

    /**
     * @return EcampCore\Entity\Event
     */
    public function Create(Camp $camp, $data)
    {
        $eventCategoryId = $data['event']['eventCategory'];
        $eventCreateFactoryId = null;

        $splitPos = strpos($eventCategoryId, '-');
        if ($splitPos !== false) {
            $eventCreateFactoryId = trim(substr($eventCategoryId, $splitPos + 1));
            $eventCategoryId = trim(substr($eventCategoryId, 0, $splitPos));
        }

        $eventCategory = $this->eventCategoryRepo->find($eventCategoryId);

        if ($eventCategory == null) {
            throw new ValidationException("Unknown EventCategory",
                array('data' => array('event' => array('eventCategory' => array('EventCategory missing' => 'Select a event category')))));
        }

        $event = new Event($camp, $eventCategory);

        $validationForm = $this->createValidationForm($event)
            ->addFieldset(new EventFieldset($camp), false);
        $validationForm->setAndValidate($data);

        if ($eventCreateFactoryId != null) {
            // apply Facotry;
            var_dump($eventCreateFactoryId);
        }

        // Create minimum of required plugins...

        $this->persist($event);

        return $event;
    }

    private function CreatePluginInstance(Event $event, PluginPrototype $prototype)
    {
        $plugin = new PluginInstance();
        $plugin->setEvent($event);
        $plugin->setPluginPrototype($prototype);

        $strategyClassName =  '\Plugin\\' . $prototype->getPlugin()->getName() . '\Strategy';
        $strategy = new $strategyClassName($this->em, $plugin);
        $strategy->persist();

        $plugin->setStrategy($strategy);
        $this->persist($plugin);

        return $plugin;
    }

    public function AddPlugin($event, $plugin)
    {
        $event = $this->Get($event);
        $plugin = $this->getPluginPrototype($plugin);
        $count = $event->countPluginsByPrototype($plugin);

        if (is_null($plugin->getMaxInstances()) || $count<$plugin->getMaxInstances()) {
            $this->CreatePluginInstance($event, $plugin);
        }
    }

    public function RemovePlugin($event, $instance)
    {
        $event 	  = $this->Get($event);
        $instance = $this->getPluginInstance($instance);
        $prototype = $instance->getPluginPrototype();

        $count = $event->countPluginsByPrototype($prototype);

        if ($count > $prototype->getMinInstances()) {
            /* cleanup plugin data */
            $instance->getStrategyInstance()->remove();

            /* remove plugin record itself */
            $this->remove($instance);
        }
    }

    /**
     * @return EcampCore\Entity\PluginInstance
     */
    public function getPluginInstance($id)
    {
        if (is_string($id)) {
            return $this->repo()->pluginInstanceRepository()->find($id);
        }

        if ($id instanceof PluginInstance) {
            return $id;
        }

        return null;
    }

    /**
     * @return EcampCore\Entity\Plugin
     */
    public function getPluginPrototype($id)
    {
        if (is_string($id)) {
            return $this->repo()->pluginPrototypeRepository()->find($id);
        }

        if ($id instanceof PluginPrototype) {
            return $id;
        }

        return null;
    }

    public function getCampOfPluginInstance($id)
    {
        $instance = $this->repo()->pluginInstanceRepository()->find($id);

        return $instance->getEvent()->getCamp();
    }

}
