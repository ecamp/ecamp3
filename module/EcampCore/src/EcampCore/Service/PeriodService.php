<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace EcampCore\Service;

use EcampCore\Entity\Camp;
use EcampCore\Entity\Period;

use EcampLib\Service\ServiceBase;
use EcampLib\Validation\ValidationException;
use EcampCore\Acl\Privilege;
use EcampCore\Validation\PeriodFieldset;

class PeriodService
    extends ServiceBase
{

    /**
     * @var DayService
     */
    private $dayService;

    public function __construct(
        DayService $dayService
    ){
        $this->dayService = $dayService;
    }

    /**
     * @param  EcampCore\Entity\Camp          $camp
     * @param  array|\ArrayAccess|Traversable $data
     * @return EcampCore\Entity\Period
     * @throws ValidationException
     */
    public function Create(Camp $camp, $data)
    {
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $period = new Period($camp);

        $validationForm = $this->createValidationForm($period)
            ->addFieldset(new PeriodFieldset());
        $validationForm->setAndValidate($data);

        $start = new \DateTime($data['period-size']['start'], new \DateTimeZone("GMT"));
        $end   = new \DateTime($data['period-size']['end'], new \DateTimeZone("GMT"));
        $numOfDays = ($end->getTimestamp() - $start->getTimestamp())/(24 * 60 * 60) + 1;

        for ($offset = 0; $offset < $numOfDays; $offset++) {
            $this->dayService->AppendDay($period);
        }

        return $this->persist($period);
    }

    /**
     * @param  Period                         $period
     * @param  array|\ArrayAccess|Traversable $data
     * @return Period
     */
    public function Update(Period $period, $data)
    {
        $camp = $period->getCamp();
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $validationForm = $this->createValidationForm($period)
            ->addFieldset(new PeriodFieldset());

        $origPeriodStart = $period->getStart();

        $validationForm->setAndValidate($data);

        $start = new \DateTime($data['period']['start'], new \DateTimeZone("GMT"));
        $end   = new \DateTime($data['period']['end'], new \DateTimeZone("GMT"));
        $numOfDays = ($end->getTimestamp() - $start->getTimestamp())/(24 * 60 * 60) + 1;

        // Change Period Length
        $oldNumOfDays = $period->getNumberOfDays();

        if ($oldNumOfDays < $numOfDays) {
            for ($offset = $oldNumOfDays; $offset < $numOfDays; $offset++) {
                $this->dayService->AppendDay($period);
            }
        }

        if ($oldNumOfDays > $numOfDays) {
            for ($offset = $numOfDays; $offset < $oldNumOfDays; $offset++) {
                $this->dayService->RemoveDay($period);
            }
        }

        // Move Startdate:
        if ($start != $origPeriodStart) {
            if (!$data['period']['moveEvents']) {
                $delta = $origPeriodStart->diff($start);
                $deltaMinuten = 24 * 60 * $delta->days;

                foreach ($period->getEventInstances() as $eventInstance) {
                    /* @var $eventInstance \EcampCore\Entity\EventInstance  */
                    $instanceOffset = $eventInstance->getOffset() - $deltaMinuten;
                    $eventInstance->setOffset($instanceOffset);

                    if ($eventInstance->getEndTime() > $period->getEnd()) {
                        throw new \Exception("Period can not be moved, because an event is sceduled outsite new period");
                    }
                }
            }
        }

        return $period;

    }

    /**
     * @param Period $period
     */
    public function Delete(Period $period)
    {
        $camp = $period->getCamp();
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $period->getDays()->clear();
        $period->getEventInstances()->clear();

        $this->remove($period);
    }
}
