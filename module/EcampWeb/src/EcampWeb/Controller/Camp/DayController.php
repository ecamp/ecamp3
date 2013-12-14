<?php

namespace EcampWeb\Controller\Camp;

class DayController extends BaseController
{
    /**
     * @return \EcampCore\Repository\DayRepository
     */
    private function getDayRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Day');
    }

    /**
     * @return \EcampCore\Repository\EventInstanceRepository
     */
    private function getEventInstanceRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\EventInstance');
    }

    public function indexAction()
    {
        $dayId = $this->params()->fromQuery('dayId');

        if ($dayId != null) {
            /* @var $period \EcampCore\Entity\Day */
            $day = $this->getDayRepository()->find($dayId);
            $eventInstances = $this->getEventInstanceRepository()->findByDay($day);
        }

        return array(
            'periods' => $this->getCamp()->getPeriods(),
            'day' => $day,
            'eventInstances' => $eventInstances
        );
    }

}
