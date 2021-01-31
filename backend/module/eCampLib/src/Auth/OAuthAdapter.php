<?php

namespace eCamp\Lib\Auth;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;

class OAuthAdapter implements AdapterInterface {
    private $identity;

    public function __construct($identity) {
        $this->identity = $identity;
    }

    public function authenticate(): Result {
        if ($this->identity) {
            return new Result(Result::SUCCESS, $this->identity);
        }

        return new Result(Result::FAILURE_UNCATEGORIZED, null);
    }
}
