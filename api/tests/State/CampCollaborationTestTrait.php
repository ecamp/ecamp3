<?php

namespace App\Tests\State;

use App\Entity\CampCollaboration;

trait CampCollaborationTestTrait {
    /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
    public static function notInvitedStatuses(): array {
        return [
            CampCollaboration::STATUS_INACTIVE => [CampCollaboration::STATUS_INACTIVE],
            CampCollaboration::STATUS_ESTABLISHED => [CampCollaboration::STATUS_ESTABLISHED],
        ];
    }
}
