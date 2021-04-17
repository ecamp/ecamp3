<?php

use eCampApi\V1\Rpc\Invitation\InvitationController;
use eCampApi\V1\Rpc\Invitation\UpdateInvitationController;
use eCampApi\V1\RpcConfig;

return RpcConfig::merge(
    RpcConfig::forRoute('e-camp-api.rpc.invitation')
        ->setController(InvitationController::class)
        ->setRoute('/api/invitations[/:inviteKey][/:campCollaborationId][/:action]')
        ->addParameterDefault('action', 'index')
        ->setAllowedHttpMethods('GET')
        ->build(),
    RpcConfig::forRoute('e-camp-api.rpc.invitation.find')
        ->setController(InvitationController::class)
        ->setRoute('/api/invitations[/:inviteKey]/find')
        ->addParameterDefault('action', 'find')
        ->setAllowedHttpMethods('GET')
        ->build(),
    RpcConfig::forRoute('e-camp-api.rpc.invitation.accept')
        ->setController(UpdateInvitationController::class)
        ->setRoute('/api/invitations[/:inviteKey]/accept')
        ->addParameterDefault('action', 'accept')
        ->setAllowedHttpMethods('POST')
        ->build(),
    RpcConfig::forRoute('e-camp-api.rpc.invitation.reject')
        ->setController(UpdateInvitationController::class)
        ->setRoute('/api/invitations[/:inviteKey]/reject')
        ->addParameterDefault('action', 'reject')
        ->setAllowedHttpMethods('POST')
        ->build(),
    RpcConfig::forRoute('e-camp-api.rpc.invitation.resend')
        ->setController(UpdateInvitationController::class)
        ->setRoute('/api/invitations[/:campCollaborationId]/resend')
        ->addParameterDefault('action', 'resend')
        ->setAllowedHttpMethods('POST')
        ->build()
);
