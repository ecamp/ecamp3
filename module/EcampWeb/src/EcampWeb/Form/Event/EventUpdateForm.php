<?php

namespace EcampWeb\Form\Event;

use EcampCore\Entity\Camp;
use EcampCore\Entity\Event;
use EcampCore\Entity\EventInstance;
use EcampCore\Validation\EventFieldset;
use EcampCore\Validation\EventInstanceFieldset;
use EcampWeb\Form\AjaxBaseForm;

class EventUpdateForm extends AjaxBaseForm
{

    public function __construct
    ( Camp $camp
    , Event $event
    , EventInstance $eventInstance
    ) {
        parent::__construct('event-update-form');

        $eventFieldset = new EventFieldset($camp, $event);
        $this->add($eventFieldset);

        $eventInstanceFieldset = new EventInstanceFieldset($camp, null, null, $eventInstance);
        $this->add($eventInstanceFieldset);
    }

}
