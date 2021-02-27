<?php

use eCampApi\V1\Rpc\Register\RegisterController;
use eCampApi\V1\RpcConfig;

return RpcConfig::forRoute('e-camp-api.rpc.register')
    ->setController(RegisterController::class)
    ->setRoute('/api/register[/:action]')
    ->addParameterDefault('action', 'register')
    ->setAllowedHttpMethods('POST')
    ->build()
;
