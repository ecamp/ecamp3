<?php

use eCampApi\RpcConfigFactory;
use eCampApi\V1\Rpc\Invitation\InvitationController;
use eCampApi\V1\Rpc\Invitation\UpdateInvitationController;
use Laminas\Stdlib\ArrayUtils;

$indexConfig = RpcConfigFactory::forRoute('e-camp-api.rpc.invitation')
    ->setController(InvitationController::class)
    ->setRoute('/api/invitations[/:inviteKey][/:action]')
    ->addParameterDefault('action', 'index')
    ->setAllowedHttpMethods('GET')
    ->build()
;

$findConfig = RpcConfigFactory::forRoute('e-camp-api.rpc.invitation.find')
    ->setController(InvitationController::class)
    ->setRoute('/api/invitations[/:inviteKey]/find')
    ->addParameterDefault('action', 'find')
    ->setAllowedHttpMethods('GET')
    ->build()
;

$updateConfig = RpcConfigFactory::forRoute('e-camp-api.rpc.invitation.accept')
    ->setController(UpdateInvitationController::class)
    ->setRoute('/api/invitations[/:inviteKey]/accept')
    ->setAllowedHttpMethods('POST')
    ->addParameterDefault('action', 'accept')
    ->build()
;
$rejectConfig = RpcConfigFactory::forRoute('e-camp-api.rpc.invitation.reject')
    ->setController(UpdateInvitationController::class)
    ->setRoute('/api/invitations[/:inviteKey]/reject')
    ->setAllowedHttpMethods('POST')
    ->addParameterDefault('action', 'reject')
    ->build()
;
$mergeGet = ArrayUtils::merge($findConfig, $indexConfig);
$mergePost = ArrayUtils::merge($rejectConfig, $updateConfig);

return ArrayUtils::merge($mergeGet, $mergePost);
