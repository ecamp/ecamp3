<?php

/**
 * Print single Event with wkhtmltopdf
 *
 * Resque job that has no Application dependencies (standalone job)
 */

namespace EcampCore\Job;

use EcampCore\Entity\Event;

class EventPrinter extends BasePdfPrinter
{

    public function __construct(Event $event = null)
    {
        parent::__construct();

        if ($event) {
            $this->eventId = $event->getId();
        }

        $this->setOption('cookie', $_COOKIE);
    }

    public function perform()
    {
        $url = __BASE_URL__ . $this->urlFromRoute(
            'web/default',
            array('controller' => 'EventPrinter', 'action' => 'print'),
            array('query' => array('eventId' => $this->eventId))
        );

        $this->setUrl($url);

        parent::perform();
    }
}
