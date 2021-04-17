<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\DayResponsible;
use eCamp\Core\Hydrator\DayResponsibleHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class DayResponsibleService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            DayResponsible::class,
            null,
            DayResponsibleHydrator::class,
            $authenticationService
        );
    }

    protected function createEntity($data): DayResponsible {
        /** @var Day $day */
        $day = $this->findRelatedEntity(Day::class, $data, 'dayId');

        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $this->findRelatedEntity(CampCollaboration::class, $data, 'campCollaborationId');

        /** @var DayResponsible $dayResponsible */
        $dayResponsible = parent::createEntity($data);
        $day->addDayResponsible($dayResponsible);
        $campCollaboration->addDayResponsible($dayResponsible);

        return $dayResponsible;
    }
}
