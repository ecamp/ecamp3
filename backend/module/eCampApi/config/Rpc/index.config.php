<?php

use eCampApi\V1\Rpc\Index\IndexController;
use eCampApi\V1\RpcConfigFactory;

return RpcConfigFactory::forRoute('e-camp-api.rpc.index')
    ->setController(IndexController::class)
    ->setRoute('/[:action]')
    ->addParameterDefault('action', 'index')
    ->setAllowedHttpMethods('GET')
    ->build()
;
