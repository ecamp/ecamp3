<?php

namespace App\State;

use App\Entity\CampCollaboration;

trait CampCollaborationSendEmailTrait {
    private function sendInviteEmail(CampCollaboration $data) {
        if (CampCollaboration::STATUS_INVITED == $data->status && $data->getEmail()) {
            /** @var User $user */
            $user = $this->security->getUser();

            $this->mailService->sendInviteToCampMail($user, $data->camp, $data->inviteKey, $data->getEmail());
        }
    }
}
