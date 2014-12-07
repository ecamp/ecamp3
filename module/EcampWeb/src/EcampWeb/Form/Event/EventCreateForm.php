<?php

namespace EcampWeb\Form\Event;

use EcampWeb\Form\AjaxBaseForm;
use EcampCore\Validation\EventFieldset;
use EcampCore\Validation\EventInstanceFieldset;
use EcampCore\Entity\Period;
use EcampCore\Entity\Camp;

class EventCreateForm extends AjaxBaseForm
{

    public function __construct(Camp $camp, Period $period = null)
    {
        parent::__construct('event-create-form');

        $eventFieldset = new EventFieldset($camp);
        $this->add($eventFieldset);

        $eventInstanceFieldset = new EventInstanceFieldset($camp, $period);
        $this->add($eventInstanceFieldset);

    }

}
