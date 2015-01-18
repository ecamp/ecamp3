<?php

namespace EcampCore\Service;

use EcampCore\Entity\Event;
use EcampCore\Entity\Camp;

use EcampCore\Plugin\StrategyProvider;
use EcampCore\Repository\EventRepository;
use EcampLib\Service\ServiceBase;
use EcampCore\Repository\EventCategoryRepository;
use EcampLib\Validation\ValidationException;

class EventService
    extends ServiceBase
{
    /**
     * @var StrategyProvider
     */
    private $strategyProvider;

    /**
     * @var \EcampCore\Repository\EventRepository
     */
    private $eventRepository;

    /**
     * @var \EcampCore\Repository\EventCategoryRepository
     */
    private $eventCategoryRepository;

    public function __construct(
        StrategyProvider $strategyProvider,
        EventRepository $eventRepository,
        EventCategoryRepository $eventCategoryRepository
    ){
        $this->strategyProvider = $strategyProvider;
        $this->eventRepository = $eventRepository;
        $this->eventCategoryRepository = $eventCategoryRepository;
    }

    /**
     * @return \EcampCore\Entity\Event | NULL
     */
    public function Get($id)
    {
        if ($id instanceof Event) {
            return $id;
        }

        if (is_string($id)) {
            return $this->eventRepository->find($id);
        }

        return null;
    }

    /**
     * @param Camp $camp
     * @param $data
     * @throws ValidationException
     * @return \EcampCore\Entity\Event
     */
    public function Create(Camp $camp, $data)
    {
        $eventCategoryId = $data['eventCategory'];
        list($eventCategory, $eventCategoryFactory) = $this->GetEventCategoryFactory($eventCategoryId);

        $event = new Event($camp, $eventCategory);

        $eventValidationForm =
            $this->createValidationForm($event, $data, array('title'));

        if (!$eventValidationForm->isValid()) {
            throw ValidationException::FromForm($eventValidationForm);
        }

        if ($eventCategoryFactory != null) {
            // apply Factory
            var_dump($eventCategoryFactory);
        }

        // Create minimum of required plugins...

        $this->persist($event);

        return $event;
    }

    public function Update(Event $event, $data)
    {
        $eventCategoryId = $data['eventCategory'];
        list($eventCategory, $eventCategoryFactory) = $this->GetEventCategoryFactory($eventCategoryId);

        $event->setEventCategory($eventCategory);

        $eventValidationForm =
            $this->createValidationForm($event, $data, array('title'));

        if (!$eventValidationForm->isValid()) {
            throw ValidationException::FromForm($eventValidationForm);
        }

        if ($eventCategoryFactory != null) {
            // apply Factory
            var_dump($eventCategoryFactory);
        }

        // Create minimum of required plugins...
        return $event;
    }

    private function GetEventCategoryFactory($eventCategoryId)
    {
        $eventCategoryFactoryId = null;
        $eventCategoryFactory = null;

        $splitPos = strpos($eventCategoryId, '-');
        if ($splitPos !== false) {
            $eventCategoryFactoryId = trim(substr($eventCategoryId, $splitPos + 1));
            $eventCategoryId = trim(substr($eventCategoryId, 0, $splitPos));
        }

        /** @var \EcampCore\Entity\EventCategory $eventCategory */
        $eventCategory = $this->eventCategoryRepository->find($eventCategoryId);

        if ($eventCategory == null) {
            throw new ValidationException("Unknown EventCategory",
                array('data' => array('event' => array('eventCategory' => array('EventCategory missing' => 'Select a event category')))));
        }

        if ($eventCategoryFactoryId != null) {
            // load EventCategoryFactory;
            var_dump($eventCategoryFactoryId);

            //$eventCategoryFactory = ...
        }

        return array($eventCategory, $eventCategoryFactory);
    }

    /**
     * @param  Event|int $event
     * @return bool
     */
    public function Delete($event)
    {
        $event = $this->Get($event);

        /** @var \EcampCore\Entity\EventPlugin $eventPlugin */
        foreach ($event->getEventPlugins() as $eventPlugin) {
            $pluginStrategy = $this->strategyProvider->Get($eventPlugin->getPlugin());
            $pluginStrategy->delete($eventPlugin);
        }

        $this->remove($event);

        return true;
    }

}
