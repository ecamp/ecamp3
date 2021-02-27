<?php

use eCampApi\V1\Rpc\Profile\ProfileController;
use eCampApi\V1\RpcConfigFactory;

return RpcConfigFactory::forRoute('e-camp-api.rpc.profile')
    ->setController(ProfileController::class)
    ->setRoute('/api/profile')
    ->addParameterDefault('action', 'index')
    ->setAllowedHttpMethods('GET', 'PATCH')
    ->build()
;
