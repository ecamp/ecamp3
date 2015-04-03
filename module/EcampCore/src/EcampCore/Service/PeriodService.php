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
     * @param  Camp                $camp
     * @param  array               $data
     * @return Period
     * @throws ValidationException
     */
    public function Create(Camp $camp, $data)
    {
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $period = new Period($camp);

        $validationForm = $this->createValidationForm($period, $data, array('start', 'description'));
        if ($validationForm->isValid()) {

            if (!isset($data['start'])) {
                throw ValidationException::ValueRequired('start');
            }
            if (!isset($data['end'])) {
                throw ValidationException::ValueRequired('end');
            }

            $start = new \DateTime($data['start'], new \DateTimeZone("GMT"));
            $end   = new \DateTime($data['end'], new \DateTimeZone("GMT"));
            $numOfDays = ($end->getTimestamp() - $start->getTimestamp())/(24 * 60 * 60) + 1;

            for ($offset = 0; $offset < $numOfDays; $offset++) {
                $this->dayService->AppendDay($period);
            }

            $this->persist($period);

        } else {
            throw ValidationException::FromForm($validationForm);
        }

        return $period;
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

        $validationForm = $this->createValidationForm($period, $data, array('start', 'description'));
        if ($validationForm->isValid()) {
            $origPeriodStart = $period->getStart();

            if (!isset($data['start'])) {
                throw ValidationException::ValueRequired('start');
            }
            if (!isset($data['end'])) {
                throw ValidationException::ValueRequired('end');
            }

            $start = new \DateTime($data['start'], new \DateTimeZone("GMT"));
            $end   = new \DateTime($data['end'], new \DateTimeZone("GMT"));
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
                if (!$data['moveEvents']) {
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
