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
use EcampCore\Validation\ValidationException;
use EcampCore\Validation\Period\PeriodEditFieldset;
use EcampCore\Validation\Period\PeriodSizeFieldset;
use EcampCore\Validation\Period\PeriodMoveFieldset;

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
            ->addFieldset(new PeriodEditFieldset())
            ->addFieldset(new PeriodSizeFieldset());

        if (!$validationForm->setData($data)->isValid()) {
            throw new ValidationException(
                "Form validation error",
                array('data' => $validationForm->getMessages())
            );
        }

        $start = new \DateTime($data['period-size']['start'], new \DateTimeZone("GMT"));
        $end   = new \DateTime($data['period-size']['end'], new \DateTimeZone("GMT"));

        $numOfDays = ($end->getTimestamp() - $start->getTimestamp())/(24 * 60 * 60) + 1;
        if ($numOfDays < 1) {
            throw new ValidationException("Minimum length of camp is 1 day.",
                array(
                    'data' => array(
                        'period-size' => array('end' => array("Minimum length of camp is 1 day."))
                    )
                )
            );
        }

        for ($offset = 0; $offset < $numOfDays; $offset++) {
            $this->dayService->AppendDay($period);
        }

        return $this->persist($period);
    }

    /**
     * @param Period $period
     * @param Params $params
     */
    public function Update(Period $period, Params $params)
    {
        $camp = $period->getCamp();
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
        $camp = $period->getCamp();
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $period->getDays()->clear();
        $period->getEventInstances()->clear();

        $this->remove($period);
    }

    /**
     * @param Period $period
     * @param array  $data
     */
    public function Move(Period $period, $data)
    {
        $camp = $period->getCamp();
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $validationForm = $this->createValidationForm($period)
            ->addFieldset(new PeriodMoveFieldset());

        if (!$validationForm->setData($data)->isValid()) {
            throw new ValidationException(
                "Form validation error",
                array('data' => $validationForm->getMessages())
            );
        }

        $start = new \DateTime($data['period-move']['start'], new \DateTimeZone("GMT"));
        $period->setStart($start);
    }

    /**
     * @param Period $period
     * @param int    $numOfDays
     */
    public function Resize(Period $period, $data)
    {
        $camp = $period->getCamp();
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $validationForm = $this->createValidationForm($period)
            ->addFieldset(new PeriodSizeFieldset());

        $start = $period->getStart();
        $end = new \DateTime($data['period-size']['end'], new \DateTimeZone("GMT"));

        $numOfDays = ($end->getTimestamp() - $start->getTimestamp())/(24 * 60 * 60) + 1;
        if ($numOfDays < 1) {
            throw new ValidationException("Minimum length of camp is 1 day.",
                array(
                    'data' => array(
                        'period-size' => array('end' => array("Minimum length of camp is 1 day."))
                    )
                )
            );
        }

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
