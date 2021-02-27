<?php

use eCampApi\RpcConfigFactory;
use eCampApi\V1\Rpc\Register\RegisterController;

return RpcConfigFactory::forRoute('e-camp-api.rpc.register')
    ->setController(RegisterController::class)
    ->setRoute('/api/register[/:action]')
    ->addParameterDefault('action', 'register')
    ->setAllowedHttpMethods('POST')
    ->build()
;
