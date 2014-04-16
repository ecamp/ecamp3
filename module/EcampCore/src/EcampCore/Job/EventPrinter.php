<?php

/**
 * Print single Event with wkhtmltopdf
 *
 * Resque job that has no Application dependencies (standalone job)
 */

namespace EcampCore\Job;

use EcampCore\Entity\Event;
use EcampLib\Job\AbstractServiceJobBase;

class EventPrinter extends AbstractServiceJobBase
{

    public function __construct(Event $event = null)
    {
        parent::__construct();

        if ($event) {
            $this->setEventId($event->getId());
        }
    }

    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    public function getEventId()
    {
        return $this->eventId;
    }

    public function perform()
    {
        $src = __BASE_URL__ . $this->urlFromRoute(
            'web/default',
            array('controller' => 'EventPrinter', 'action' => 'print'),
            array('query' => array('eventId' => $this->getEventId()))
        );
        $target = __DATA__ . "/printer/" . $this->getToken() . ".pdf";

        $pdf = new \EcampLib\Pdf\WkHtmlToPdf();
        $pdf->generate($src, $target);
    }
}
