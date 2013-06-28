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

use EcampCore\Entity\Day;
use EcampCore\Entity\Camp;
use EcampCore\Entity\Period;

use EcampLib\Service\Params\Params;
use EcampLib\Service\ServiceBase;
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
     * @param  Params                $params
     * @return CoreApi\Entity\Period
     */
    public function Create(Camp $camp, Params $params)
    {
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        if ( $params->getValue('start') == "") {
            $params->addError('start', "Date cannot be empty.");
            $this->validationFailed();
        }

        if ( $params->getValue('end') == "" ) {
            $params->addError('end', "Date cannot be empty.");
            $this->validationFailed();
        }

        $period = new Period($camp);
        $this->persist($period);

        $start = new \DateTime($params->getValue('start'), new \DateTimeZone("GMT"));
        $end   = new \DateTime($params->getValue('end'), new \DateTimeZone("GMT"));
        $desc  = $params->hasElement('description') ? $params->getValue('description') : "";

        $period->setStart($start);
        $period->setDescription($desc);

        $numOfDays = ($end->getTimestamp() - $start->getTimestamp())/(24 * 60 * 60) + 1;
        if ($numOfDays < 1) {
            $params->addError('end', "Minimum length of camp is 1 day.");
            $this->validationFailed();
        }

        for ($offset = 0; $offset < $numOfDays; $offset++) {
            $this->dayService->AppendDay($period);
        }

        return $period;
    }

    /**
     * @param Period $period
     * @param Params $params
     */
    public function Update(Period $period, Params $params)
    {
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        if ($params->hasElement('description')) {
            $period->setDescription($params->getValue('description'));
        }
    }

    /**
     * @param Period $period
     */
    public function Delete(Period $period)
    {
        $this->aclRequire($camp, Privilege::CAMP_ADMINISTRATE);

        $period->getDays()->clear();
        $period->getEventInstances()->clear();

        $this->remove($period);
    }

    /**
     * @param Period       $period
     * @param unknown_type $newStart
     */
    public function Move(Period $period, $newStart)
    {
        $camp = $period->getCamp();
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        if (! $newStart instanceof \DateTime) {
            $newStart = new \DateTime($newStart, new \DateTimeZone("GMT"));
        }

        $period->setStart($newStart);
    }

    /**
     * @param Period $period
     * @param int    $numOfDays
     */
    public function Resize(Period $period, $numOfDays)
    {
        $camp = $period->getCamp();
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $oldNumOfDays = $period->getNumberOfDays();

        if ($oldNumOfDays < $numOfDays) {
            for ($offset = $oldNumOfDays; $offset < $numOfDays; $offset++) {
                $this->dayService->AppendDay($period);
            }

            return;
        }

        if ($oldNumOfDays > $numOfDays) {
            for ($offset = $numOfDays; $offset < $oldNumOfDays; $offset++) {
                $this->dayService->RemoveDay($period);
            }
        }
    }

}
