<?php

namespace eCamp\Lib\Mail;

interface ProviderInterface {
    function sendMail(MessageData $message);
}