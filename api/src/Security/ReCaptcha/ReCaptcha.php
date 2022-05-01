<?php

namespace App\Security\ReCaptcha;

class ReCaptcha {
    public function __construct(
        private string $reCaptchaSecret,
        private \ReCaptcha\ReCaptcha $reCaptcha,
    ) {
    }

    public function verify($response, $remoteIp = null) {
        if ('disabled' != strtolower($this->reCaptchaSecret)) {
            return $this->reCaptcha->verify($response, $remoteIp);
        }

        // if no reCaptchaSecret (dev & test) -> auto-success
        return new \ReCaptcha\Response(true);
    }
}
