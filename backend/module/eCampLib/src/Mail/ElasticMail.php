<?php

namespace eCamp\Lib\Mail;

use eCamp\Lib\Mail\ProviderInterface;
use SlmMail\Mail\Message\ElasticEmail;
use Laminas\Mail\Transport\TransportInterface;

class ElasticMail implements ProviderInterface {
    /** @var TransportInterface */
    private $mailTransport;
    
    public function __construct(TransportInterface $mailTransport) {
        $this->mailTransport = $mailTransport;
    }

    public function sendMail(MessageData $data) {

        $mail = new ElasticEmail();
        $mail->setFrom($data->from);
        $mail->setTo($data->to);

        if ($data->cc != null) {
            $mail->setCc($data->cc);
        }
        if ($data->bcc != null) {
            $mail->setBcc($data->bcc);
        }

        $mail->setSubject($data->subject);
        $mail->setTemplate($data->template);

        $this->mailTransport->send($mail);
    }
}