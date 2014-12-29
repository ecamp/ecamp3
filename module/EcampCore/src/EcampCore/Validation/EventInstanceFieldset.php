<?php

namespace EcampCore\Validation;

use EcampCore\Entity\EventInstance;
use EcampCore\Entity\Period;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use EcampCore\Entity\Camp;

class EventInstanceFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(Camp $camp, Period $period = null, EventInstance $eventInstance = null)
    {
        parent::__construct('eventInstance');

        if($period == null && $eventInstance != null){
            $period = $eventInstance->getPeriod();
        }

        if($eventInstance != null){
            $starttime = $eventInstance->getStartTime();
            $endtime = $eventInstance->getEndTime();

            $startOffset = $eventInstance->getOffsetInMinutes();
            $endOffset = $startOffset + $eventInstance->getDurationInMinutes();

            $startdayIdx = floor($startOffset / 1440);
            $enddayIdx = floor($endOffset / 1440);
            $startday = null;
            $endday = null;
        }


        /*
        $startDay = $endDay = $day;
        if ($period != null && $startMin != null) {
            $startDay = $period->getDay(floor($startMin / 1440));
        }
        if ($period != null && $endMin != null) {
            $endDay = $period->getDay(floor($endMin / 1440));
        }

        $startDayId = $startDay != null ? $startDay->getId() : null;
        $endDayId = $endDay != null ? $endDay->getId() : null;
        */

        $startDayValueOptions = array();
        $endDayValueOptions = array();

        $periods = ($period == null) ? $camp->getPeriods() : array($period);
        foreach ($periods as $p) {
            $startDayValueOptions[$p->getId()] = array(
                'label' => $p->getDescription(),
                'options' => array(),
                'attributes' => array(
                    'data-period' => $p->getId()
                )
            );

            $days = $p->getDays();
            foreach ($days as $idx => $d) {
                $startDayValueOptions[$p->getId()]['options'][$d->getId()] = array(
                    'value' => $d->getId(),
                    'label' => $d->getStart()->format('D :: d.m.Y'),
                    'attributes' => array(
                        'data-timestamp' => $d->getStart()->getTimestamp(),
                        'data-period' => $p->getId()
                    )
                );

                $endDayValueOptions[$d->getId()] = array(
                    'value' => $d->getId(),
                    'label' => $d->getStart()->format('D :: d.m.Y'),
                    'attributes' => array(
                        'data-timestamp' => $d->getStart()->getTimestamp(),
                        'data-period' => $p->getId()
                    )
                );

                if($p == $period){
                    if($idx == $startdayIdx){ $startday = $d; }
                    if($idx == $enddayIdx){ $endday = $d; }
                }
            }
        }

        $this->add(array(
            'name' => 'startday',
            'type' => 'Select',
            'options' => array(
                'label' => 'Start day',
                'value_options' => $startDayValueOptions
            ),
            'attributes' => array(
                'id' => 'eventInstanceStartDay',
                'class' => 'selectpicker',
                //'disabled' => ($day != null && $period != null),
                'data-hide-disabled' => true,
            )
        ));

        $this->add(array(
            'name' => 'endday',
            'type' => 'Select',
            'options' => array(
                'label' => 'End day',
                'value_options' => $endDayValueOptions
            ),
            'attributes' => array(
                'id' => 'eventInstanceEndDay',
                'class' => 'selectpicker',
                'data-hide-disabled' => true,
            )
        ));

        $this->add(array(
            'name' => 'starttime',
            'options' => array(
                'label' => 'Time',
            ),
            'type' => 'text',
                'attributes' => array(
                'id' => 'eventInstanceStarttime',
                'type' => 'time'
            )
        ));

        $this->add(array(
            'name' => 'endtime',
            'options' => array(
                'label' => 'Time',
            ),
            'type' => 'text',
            'attributes' => array(
                'id' => 'eventInstanceEndtime',
                'type' => 'time'
            )
        ));

        // $this->get('startday')->setValue();
        // $this->get('endday')->setValue();

        if($starttime != null) {
            $this->get('starttime')->setValue($starttime->format('H:i'));
        }
        if($endtime != null) {
            $this->get('endtime')->setValue($endtime->format('H:i'));
        }

        if($startday != null){
            $this->get('startday')->setValue($startday);
        }

        if($endday != null){
            $this->get('endday')->setValue($endday);
        }
        /*
        if ($startMin) {
            $startTime = new \DateTime();
            $startTime->setTime(0, $startMin);
            $this->get('starttime')->setValue($startTime->format('H:i'));
        }

        if ($endMin) {
            $endTime = new \DateTime();
            $endTime->setTime(0, $endMin);
            $this->get('endtime')->setValue($endTime->format('H:i'));
        }
        */

    }

    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name' => 'starttime',
                'required' => true,
            ),
            array(
                'name' => 'endtime',
                'required' => true,
            ),
        );
    }
}
