<?php

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\State\PersonalInvitationAcceptProcessor;
use App\State\PersonalInvitationProvider;
use App\State\PersonalInvitationRejectProcessor;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * An invitation for a person who already has an account to collaborate in a camp.
 */
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            provider: PersonalInvitationProvider::class,
        ),
        new Patch(
            uriTemplate: '/personal_invitations/{id}/'.self::ACCEPT.'{._format}',
            openapi: new OpenApiOperation(summary: 'Accept a personal invitation.'),
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            denormalizationContext: ['groups' => ['write']],
            security: 'is_authenticated()',
            validationContext: ['groups' => ['Default', 'accept']],
            output: PersonalInvitation::class,
            provider: PersonalInvitationProvider::class,
            processor: PersonalInvitationAcceptProcessor::class
        ),
        new Patch(
            uriTemplate: '/personal_invitations/{id}/'.self::REJECT.'{._format}',
            openapi: new OpenApiOperation(summary: 'Reject a personal invitation.'),
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            denormalizationContext: ['groups' => ['write']],
            output: PersonalInvitation::class,
            provider: PersonalInvitationProvider::class,
            processor: PersonalInvitationRejectProcessor::class
        ),
        new GetCollection(
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            security: 'is_authenticated()',
            provider: PersonalInvitationProvider::class,
        ),
    ],
)]
class PersonalInvitation {
    public const ACCEPT = 'accept';
    public const REJECT = 'reject';
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => ['read'],
        'swagger_definition_name' => 'read',
    ];

    #[ApiProperty(readable: true, writable: false, identifier: true, example: '1a2b3c4d')]
    #[Groups('read')]
    public string $id;

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

    public function __construct(string $id, string $campId, string $campTitle) {
        $this->id = $id;
        $this->campId = $campId;
        $this->campTitle = $campTitle;
    }
}
