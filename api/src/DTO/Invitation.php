<?php

namespace App\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'find' => [
            'method' => 'GET',
            'path' => '/{inviteKey}/find',
        ],
    ],
    routePrefix: '/invitations'
)]
class Invitation {
    #[ApiProperty(readable: false, writable: false, identifier: true, example: 'myInviteKey')]
    public string $inviteKey;
    public string $campId;
    public string $campTitle;
    public ?string $userDisplayName;
    public ?bool $userAlreadyInCamp;

    public function __construct(string $inviteKey, string $campId, string $campTitle, ?string $userDisplayName, ?bool $userAlreadyInCamp) {
        $this->inviteKey = $inviteKey;
        $this->campId = $campId;
        $this->campTitle = $campTitle;
        $this->userDisplayName = $userDisplayName;
        $this->userAlreadyInCamp = $userAlreadyInCamp;
    }
}
