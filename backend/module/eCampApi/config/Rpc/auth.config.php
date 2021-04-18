<?php

use eCampApi\V1\Rpc\Auth\AuthController;
use eCampApi\V1\RpcConfig;

return RpcConfig::merge(
    //only define collection query whitelist once, because it will me merged on controller basis.
    RpcConfig::forRoute('e-camp-api.rpc.auth.pbsmidata')
        ->setController(AuthController::class)
        ->setRoute('/api/auth/pbsmidata')
        ->addParameterDefault('action', 'pbsmidata')
        ->setAllowedHttpMethods('GET', 'POST')
        ->build(),
    //only define collection query whitelist once, because it will me merged on controller basis.
    RpcConfig::forRoute('e-camp-api.rpc.auth.google')
        ->setController(AuthController::class)
        ->setRoute('/api/auth/google')
        ->addParameterDefault('action', 'google')
        ->setAllowedHttpMethods('GET', 'POST')
        ->build(),
    //use $eCampAuth as the last, that the route name for the controller
    //when setting the allowed HTTP methods is e-camp-api.rpc.auth
    RpcConfig::forRoute('e-camp-api.rpc.auth')
        ->setController(AuthController::class)
        ->setRoute('/api/auth[/:action]')
        ->addParameterDefault('action', 'index')
        ->setAllowedHttpMethods('GET', 'POST')
        ->setCollectionQueryWhiteList('callback')
        ->build()
);
