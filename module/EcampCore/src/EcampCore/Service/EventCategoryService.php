<?php

namespace EcampCore\Service;

use EcampCore\Acl\Privilege;
use EcampCore\Entity\Camp;
use EcampCore\Entity\EventCategory;
use EcampCore\Repository\EventTypeRepository;
use EcampLib\Service\ServiceBase;
use EcampLib\Validation\ValidationException;

class EventCategoryService extends ServiceBase
{

    /** @var EventTypeRepository */
    private $eventTypeRepo = null;

    /**
     * @param EventTypeRepository $eventTypeRepo
     */
    public function __construct(
        EventTypeRepository $eventTypeRepo
    ){
        $this->eventTypeRepo = $eventTypeRepo;
    }

    public function Create(Camp $camp, $data)
    {
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        if (!isset($data['eventType'])) {
            throw ValidationException::ValueRequired('eventType');
        }

        /** @var \EcampCore\Entity\EventCategory $eventType */
        $eventType = $this->eventTypeRepo->find($data['eventType']);

        if (!$camp->getCampType()->getEventTypes()->contains($eventType)) {
            throw new ValidationException(array(
                'eventType' => array('Selected EventType is not allowed for this CampType')
            ));
        }

        $category = new EventCategory($camp, $eventType);

        $validationForm = $this->createValidationForm($category, $data, array('short', 'name', 'numberingStyle', 'color'));
        if ($validationForm->isValid()) {
            $this->persist($category);
        } else {
            throw ValidationException::FromForm($validationForm);
        }

        return $category;
    }
}
