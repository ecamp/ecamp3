<?php

namespace eCamp\Lib\Mail;

interface ProviderInterface {
    public function sendMail(MessageData $message);
}
