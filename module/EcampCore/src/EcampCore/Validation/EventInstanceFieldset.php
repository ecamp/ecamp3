<?php

namespace EcampCore\Validation;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use EcampCore\Entity\Camp;
use EcampCore\Entity\Period;
use EcampCore\Entity\Day;

class EventInstanceFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(Camp $camp, Period $period = null, Day $day = null)
    {
        parent::__construct('eventInstance');

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

            $days = /*($period != null && $day != null) ? array($day) :*/ $p->getDays();
            foreach ($days as $d) {
                $startDayValueOptions[$p->getId()]['options'][$d->getId()] = array(
                    'value' => $d->getId(),
                    'label' => $d->getStart()->format('D :: d.m.Y'),
                    'selected' => ($d == $day),
                    'attributes' => array(
                        'data-timestamp' => $d->getStart()->getTimestamp(),
                        'data-period' => $p->getId()
                    )
                );

                $endDayValueOptions[$d->getId()] = array(
                    'value' => $d->getId(),
                    'label' => $d->getStart()->format('D :: d.m.Y'),
                    'selected' => ($d == $day),
                    'attributes' => array(
                        'data-timestamp' => $d->getStart()->getTimestamp(),
                        'data-period' => $p->getId()
                    )
                );
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
