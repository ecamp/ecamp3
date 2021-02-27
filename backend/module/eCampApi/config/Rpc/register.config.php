<?php

use eCampApi\V1\Rpc\Register\RegisterController;
use eCampApi\V1\RpcConfigFactory;

return RpcConfigFactory::forRoute('e-camp-api.rpc.register')
    ->setController(RegisterController::class)
    ->setRoute('/api/register[/:action]')
    ->addParameterDefault('action', 'register')
    ->setAllowedHttpMethods('POST')
    ->build()
;
