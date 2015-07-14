<?php

namespace EcampCore\Printable;

use EcampCore\Entity\Medium;
use EcampCore\Entity\EventInstance as EventInstanceEntity;
use EcampCore\Plugin\StrategyProvider;
use EcampCore\Repository\EventInstanceRepository;
use EcampCore\Repository\EventTemplateRepository;
use EcampCore\Repository\MediumRepository;
use EcampCore\View\Event\EventTemplateRenderer;
use EcampLib\Printable\PrintableInterface;

class EventInstance implements PrintableInterface
{
    /** @var StrategyProvider */
    private $strategyProvider;

    /** @var MediumRepository */
    private $mediumRepository;

    /** @var EventTemplateRepository */
    private $eventTemplateRepository;

    /** @var EventInstanceRepository */
    private $eventInstanceRepository;

    public function __construct(
        StrategyProvider $strategyProvider,
        MediumRepository $mediumRepository,
        EventTemplateRepository $eventTemplateRepository,
        EventInstanceRepository $eventInstanceRepository
    ) {
        $this->strategyProvider = $strategyProvider;
        $this->mediumRepository = $mediumRepository;
        $this->eventTemplateRepository = $eventTemplateRepository;
        $this->eventInstanceRepository = $eventInstanceRepository;
    }

    public function create(array $item)
    {
        $eventInstanceId = $item['eventInstanceId'];

        /** @var \EcampCore\Entity\EventInstance $eventInstance */
        $eventInstance = $this->eventInstanceRepository->find($eventInstanceId);

        return $this->getEventInstanceViewModel($eventInstance);
    }

    protected function getEventInstanceViewModel(EventInstanceEntity $eventInstance)
    {
        $event = $eventInstance->getEvent();
        $printMedium = $this->getPrintMedium();

        $eventTemplate = $this->eventTemplateRepository->findTemplate($event, $printMedium);
        $eventTemplateRenderer = new EventTemplateRenderer($this->strategyProvider, $eventTemplate);
        $eventTemplateRenderer->buildRendererTree();

        return $eventTemplateRenderer->render($event, $eventInstance);
    }

    /**
     * @return \EcampCore\Entity\Medium
     */
    protected function getPrintMedium()
    {
        return $this->mediumRepository->find(Medium::MEDIUM_PRINT);
    }
}
