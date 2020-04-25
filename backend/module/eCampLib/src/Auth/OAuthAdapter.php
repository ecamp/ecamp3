<?php

namespace eCamp\Lib\Auth;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class OAuthAdapter implements AdapterInterface {
    private $identity;

    public function __construct($identity) {
        $this->identity = $identity;
    }

    /**
     * @return Result
     */
    public function authenticate() {
        if ($this->identity) {
            return new Result(Result::SUCCESS, $this->identity);
        }

        return new Result(Result::FAILURE_UNCATEGORIZED, null);
    }
}
