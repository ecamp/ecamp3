<?php

namespace EcampApi\Serializer;

use EcampCore\Entity\Camp;
use EcampCore\Entity\Period;

class PeriodSerializer extends BaseSerializer
{
    public function serialize($period)
    {
        $campSerializer = new CampSerializer($this->format, $this->router);
        $daySerializer = new DaySerializer($this->format, $this->router);
        $eventInstanceSerializer = new EventInstanceSerializer($this->format, $this->router);

        return array(
            'id' 				=> 	$period->getId(),
            'href'				=>	$this->getPeriodHref($period),
            'start'				=> 	$period->getStart()->getTimestamp(),
            'end'				=> 	$period->getEnd()->getTimestamp(),
            'numDays'			=> 	$period->getNumberOfDays(),
            'camp'				=>  $campSerializer->getReference($period->getCamp()),
            'days'				=>	$daySerializer->getCollectionReference($period),
            'eventInstances'	=> 	$eventInstanceSerializer->getCollectionReference($period),
        );
    }

    public function getReference(Period $period = null)
    {
        if ($period == null) {
            return null;
        } else {
            return array(
                'id'	=>	$period->getId(),
                'href'	=>	$this->getPeriodHref($period)
            );
        }
    }

    public function getCollectionReference($collectionOwner)
    {
        if ($collectionOwner instanceof Camp) {
            return array('href' => $this->getCamp_PeriodCollectionHref($collectionOwner));
        }

        return null;
    }

    private function getPeriodHref(Period $period)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'periods',
                    'action' => 'get',
                    'id' => $period->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/rest'
                )
        );
    }

    private function getCamp_PeriodCollectionHref(Camp $camp)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'periods',
                    'action' => 'getList',
                    'camp' => $camp->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/camp/rest'
                )
            );
    }
}
