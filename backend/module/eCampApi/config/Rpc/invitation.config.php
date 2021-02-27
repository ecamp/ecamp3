<?php

use eCampApi\RpcConfigFactory;
use eCampApi\V1\Rpc\Invitation\InvitationController;

return RpcConfigFactory::forRoute('e-camp-api.rpc.invitation')
    ->setController(InvitationController::class)
    ->setRoute('/api/invitations[/:inviteKey][/:action]')
    ->addParameterDefault('action', 'index')
    ->setAllowedHttpMethods('GET', 'POST')
    ->build()
;
