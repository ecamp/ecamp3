<?php

namespace eCamp\Lib\Mail;

use Laminas\Mail\Transport\TransportInterface;
use SlmMail\Mail\Message\ElasticEmail;

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

        if (null != $data->cc) {
            $mail->setCc($data->cc);
        }
        if (null != $data->bcc) {
            $mail->setBcc($data->bcc);
        }

        $mail->setSubject($data->subject);
        $mail->setTemplate($data->template);

        $this->mailTransport->send($mail);
    }
}
