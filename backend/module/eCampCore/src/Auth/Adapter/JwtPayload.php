<?php

namespace eCamp\Core\Auth\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class JwtPayload implements AdapterInterface {
    private $userId;
    private $role;

    public function __construct($userId, $role) {
        $this->userId = $userId;
        $this->role = $role;
    }

    /**
     * @return Result
     */
    public function authenticate() {
        if ($this->userId && $this->role) {
            return new Result(Result::SUCCESS, [ 'id' => $this->userId, 'role' => $this->role ]);
        }
        return new Result(Result::FAILURE_UNCATEGORIZED, null);
    }
}
