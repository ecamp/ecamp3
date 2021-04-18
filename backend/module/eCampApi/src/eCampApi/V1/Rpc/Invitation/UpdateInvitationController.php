<?php

namespace eCampApi\V1\Rpc\Invitation;

use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\ApiTools\Rpc\RpcController;

/**
 * Because laminas api-tools-rcp only allows to configure the HTTP methods per Controller,
 * we use this controller for POST actions.
 */
class UpdateInvitationController extends RpcController {
    private InvitationController $invitationController;

    public function __construct(InvitationController $invitationController) {
        $this->invitationController = $invitationController;
    }

    /**
     * @throws \eCamp\Lib\Acl\NoAccessException
     * @throws \eCamp\Lib\Service\EntityNotFoundException
     * @throws \eCamp\Lib\Service\EntityValidationException
     */
    public function accept($inviteKey): HalJsonModel {
        return $this->invitationController->accept($inviteKey);
    }

    /**
     * @throws \eCamp\Lib\Service\EntityNotFoundException
     */
    public function reject($inviteKey): HalJsonModel {
        return $this->invitationController->reject($inviteKey);
    }

    /**
     * @throws \eCamp\Lib\Acl\NoAccessException
     * @throws \eCamp\Lib\Service\EntityNotFoundException
     * @throws \eCamp\Lib\Acl\NotAuthenticatedException
     * @throws \eCamp\Lib\Service\EntityValidationException
     */
    public function resend($campCollaborationId): HalJsonModel {
        return $this->invitationController->resend($campCollaborationId);
    }
}
