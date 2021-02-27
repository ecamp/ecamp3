<?php

use eCampApi\RpcConfigFactory;
use eCampApi\V1\Rpc\Auth\AuthController;
use Laminas\Stdlib\ArrayUtils;

$eCampAuth = RpcConfigFactory::forRoute('e-camp-api.rpc.auth')
    ->setController(AuthController::class)
    ->setRoute('/api/auth[/:action]')
    ->addParameterDefault('action', 'index')
    ->setAllowedHttpMethods('GET', 'POST')
    ->setCollectionQueryWhiteList('callback')
    ->build()
;

//only define collection query whitelist once, because it will me merged on controller basis.
$googleAuth = RpcConfigFactory::forRoute('e-camp-api.rpc.auth.google')
    ->setController(AuthController::class)
    ->setRoute('/api/auth/google')
    ->addParameterDefault('action', 'google')
    ->setAllowedHttpMethods('GET', 'POST')
    ->build()
;

//only define collection query whitelist once, because it will me merged on controller basis.
$pbsMiDataAuth = RpcConfigFactory::forRoute('e-camp-api.rpc.auth.pbsmidata')
    ->setController(AuthController::class)
    ->setRoute('/api/auth/pbsmidata')
    ->addParameterDefault('action', 'pbsmidata')
    ->setAllowedHttpMethods('GET', 'POST')
    ->build()
;
//use $eCampAuth as the last, that the route name for the controller
//when setting the allowed HTTP methods is e-camp-api.rpc.auth
$firstMerge = ArrayUtils::merge($pbsMiDataAuth, $googleAuth);

return ArrayUtils::merge($firstMerge, $eCampAuth);
