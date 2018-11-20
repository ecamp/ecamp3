<?php

namespace eCamp\Lib\Auth\MvcAuth;

use ZF\MvcAuth\Identity\AuthenticatedIdentity;

class JwtIdentity extends AuthenticatedIdentity {

    /** @var array */
    private $jwtData;

    /**
     * @param \stdClass|null $jwtData
     */
    public function __construct($jwtData = null) {
        if ($jwtData) {
            $jwtData = (array)$jwtData;
            parent::__construct($jwtData['id']);
            $this->setName($jwtData['id']);
        }
        $this->jwtData = $jwtData;
    }

    /** @return array */
    public function getIdentity() {
        return $this->jwtData;
    }
}
