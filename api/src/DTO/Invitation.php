<?php

namespace App\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * An invitation for a person to collaborate in a camp. The person may or may not
 * already have an account.
 */
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'find' => [
            'method' => 'GET',
            'path' => '/{inviteKey}/find',
            'openapi_context' => [
                'description' => 'Use myInviteKey to find an invitation in the dev environment.',
            ],
        ],
    ],
    routePrefix: '/invitations'
)]
class Invitation {
    #[ApiProperty(readable: false, writable: false, identifier: true, example: 'myInviteKey')]
    public string $inviteKey;

    /**
     * The id of the camp for which this invitation is valid. This is useful for
     * redirecting the user to the correct place after they accept.
     */
    #[ApiProperty(writable: false, example: '1a2b3c4d')]
    public string $campId;

    /**
     * The full title of the camp for which this invitation is valid. This should help
     * the user to decide whether to accept or reject the invitation.
     */
    #[ApiProperty(writable: false, example: 'Abteilungs-Sommerlager 2022')]
    public string $campTitle;

    /**
     * The display name of the user that is invited. May be null in case the user does
     * not already have an account.
     */
    #[ApiProperty(writable: false, example: 'Robert Baden-Powell')]
    public ?string $userDisplayName;

    /**
     * Indicates whether the logged in user is already collaborating in the camp, and
     * can therefore not accept the invitation.
     */
    #[ApiProperty(writable: false, example: true)]
    public ?bool $userAlreadyInCamp;

    public function __construct(string $inviteKey, string $campId, string $campTitle, ?string $userDisplayName, ?bool $userAlreadyInCamp) {
        $this->inviteKey = $inviteKey;
        $this->campId = $campId;
        $this->campTitle = $campTitle;
        $this->userDisplayName = $userDisplayName;
        $this->userAlreadyInCamp = $userAlreadyInCamp;
    }
}
