<?php

use eCampApi\RpcConfigFactory;
use eCampApi\V1\Rpc\Profile\ProfileController;

return RpcConfigFactory::forRoute('e-camp-api.rpc.profile')
    ->setController(ProfileController::class)
    ->setRoute('/api/profile')
    ->addParameterDefault('action', 'index')
    ->setAllowedHttpMethods('GET', 'PATCH')
    ->build()
;
