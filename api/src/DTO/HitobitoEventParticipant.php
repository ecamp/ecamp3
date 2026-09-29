<?php

declare(strict_types=1);

namespace App\DTO;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\State\HitobitoEventParticipantProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/hitobito/{provider}/events/{eventId}/participants{._format}',
            uriVariables: [
                'provider' => new Link(parameterName: 'provider', identifiers: ['provider']),
                'eventId' => new Link(parameterName: 'eventId', identifiers: ['eventId']),
            ],
            openapi: new OpenApiOperation(description: 'Get all leaders and co-leaders of a Hitobito event.'),
            normalizationContext: ['groups' => ['read']],
            security: 'is_authenticated()',
            provider: HitobitoEventParticipantProvider::class,
        ),
    ],
)]
class HitobitoEventParticipant {
    #[Groups(['read'])]
    public ?string $firstName = null;

    #[Groups(['read'])]
    public ?string $lastName = null;

    #[Groups(['read'])]
    public ?string $nickname = null;

    #[Groups(['read'])]
    public string $email;

    public function __construct(?string $firstName, ?string $lastName, ?string $nickname, string $email) {
        // these fields may be an empty string at Hitobito, normalize to null
        if ('' != $firstName) {
            $this->firstName = $firstName;
        }
        if ('' != $lastName) {
            $this->lastName = $lastName;
        }
        if ('' != $nickname) {
            $this->nickname = $nickname;
        }
        $this->email = $email;
    }
}
