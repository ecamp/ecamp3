<?php

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An invitation for a person to collaborate in a camp. The person may or may not
 * already have an account.
 */
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/{inviteKey}/find.{_format}',
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            openapiContext: ['description' => 'Use myInviteKey to find an invitation in the dev environment.']
        ),
        new Patch(
            security: 'is_authenticated()',
            uriTemplate: '/{inviteKey}/'.self::ACCEPT.'.{_format}',
            denormalizationContext: ['groups' => ['write']],
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            openapiContext: ['summary' => 'Accept an Invitation.', 'description' => 'Use myInviteKey2 to accept an invitation in dev environment.'],
            validationContext: ['groups' => ['Default', 'accept']]
        ),
        new Patch(
            uriTemplate: '/{inviteKey}/'.self::REJECT.'.{_format}',
            denormalizationContext: ['groups' => ['write']],
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            openapiContext: ['summary' => 'Reject an Invitation.', 'description' => 'Use myInviteKey to reject an invitation in dev environment.']
        ),
        new GetCollection(
            security: 'false',
            uriTemplate: '',
            openapiContext: ['description' => 'Not implemented. Only needed that we can show this endpoint in /index.jsonhal.']
        ),
    ],
    routePrefix: '/invitations'
)]
class Invitation {
    public const ACCEPT = 'accept';
    public const REJECT = 'reject';
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => ['read'],
        'swagger_definition_name' => 'read',
    ];

    #[ApiProperty(readable: false, writable: false, identifier: true, example: 'myInviteKey')]
    public string $inviteKey;

    /**
     * The id of the camp for which this invitation is valid. This is useful for
     * redirecting the user to the correct place after they accept.
     */
    #[ApiProperty(writable: false, example: '1a2b3c4d')]
    #[Groups('read')]
    public string $campId;

    /**
     * The full title of the camp for which this invitation is valid. This should help
     * the user to decide whether to accept or reject the invitation.
     */
    #[ApiProperty(writable: false, example: 'Abteilungs-Sommerlager 2022')]
    #[Groups('read')]
    public string $campTitle;

    /**
     * The display name of the user that is invited. May be null in case the user does
     * not already have an account.
     */
    #[ApiProperty(writable: false, example: 'Robert Baden-Powell')]
    #[Groups('read')]
    public ?string $userDisplayName;

    /**
     * Indicates whether the logged in user is already collaborating in the camp, and
     * can therefore not accept the invitation.
     */
    #[ApiProperty(writable: false, example: false)]
    #[Groups('read')]
    #[Assert\IsFalse(message: 'This user is already associated with the camp.', groups: ['accept'])]
    public ?bool $userAlreadyInCamp;

    public function __construct(string $inviteKey, string $campId, string $campTitle, ?string $userDisplayName, ?bool $userAlreadyInCamp) {
        $this->inviteKey = $inviteKey;
        $this->campId = $campId;
        $this->campTitle = $campTitle;
        $this->userDisplayName = $userDisplayName;
        $this->userAlreadyInCamp = $userAlreadyInCamp;
    }
}
