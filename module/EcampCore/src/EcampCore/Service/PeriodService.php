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

use EcampCore\Validation\PeriodFieldset;

use EcampCore\Validation\ValidationException;

use EcampCore\Validation\EntityForm;

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
     * @param  EcampCore\Entity\Camp          $camp
     * @param  array|\ArrayAccess|Traversable $data
     * @return EcampCore\Entity\Period
     * @throws ValidationException
     */
    public function Create(Camp $camp, Array $data)
    {
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $period = new Period($camp);

        $periodFieldset = new PeriodFieldset($this->getEntityManager());
        $form = new EntityForm($this->getEntityManager(), $periodFieldset, $period);

        if ( !$form->setDataAndValidate($data) ) {
            throw new ValidationException("Form validation error", array('data' => $form->getMessages()));
        }

        $start = new \DateTime($data['period']['start'], new \DateTimeZone("GMT"));
        $end   = new \DateTime($data['period']['end'], new \DateTimeZone("GMT"));

        $numOfDays = ($end->getTimestamp() - $start->getTimestamp())/(24 * 60 * 60) + 1;
        if ($numOfDays < 1) {
            throw new ValidationException("Minimum length of camp is 1 day.", array('data' => array('period' => array('end' => array("Minimum length of camp is 1 day.")))));
        }

        for ($offset = 0; $offset < $numOfDays; $offset++) {
            $this->dayService->AppendDay($period);
        }

        $this->persist($period);

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
