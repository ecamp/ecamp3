<?php

declare(strict_types=1);

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\State\HitobitoEventProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/hitobito/{provider}/events/{eventId}{._format}',
            uriVariables: [
                'provider' => new Link(parameterName: 'provider', identifiers: ['provider']),
                'eventId' => new Link(parameterName: 'eventId', identifiers: ['id']),
            ],
            openapi: new OpenApiOperation(description: 'Get a Hitobito event by its id.'),
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            security: 'is_authenticated()',
            provider: HitobitoEventProvider::class,
        ),
        new GetCollection(
            uriTemplate: '/hitobito/{provider}/events{._format}',
            uriVariables: [
                'provider' => new Link(parameterName: 'provider', identifiers: ['provider']),
            ],
            openapi: new OpenApiOperation(description: 'Get all accessible Hitobito events.'),
            normalizationContext: ['groups' => ['read']],
            security: 'is_authenticated()',
            provider: HitobitoEventProvider::class,
        ),
    ],
)]
class HitobitoEvent {
    // only show motto, location and dates when fetching a specific event
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => ['read', 'details'],
    ];

    public string $provider;

    #[ApiProperty(identifier: true, example: '1234')]
    #[Groups(['read'])]
    public int $id;

    #[Groups(['read'])]
    public string $name;

    /**
     * Whether a camp has already been created from this Hitobito event.
     */
    #[ApiProperty(writable: false, example: true)]
    #[Groups(['read'])]
    public bool $isImported = false;

    #[Groups(['details'])]
    public ?string $motto = null;

    #[Groups(['details'])]
    public ?string $location = null;

    /**
     * @var HitobitoEventDate[]
     */
    #[Groups(['details'])]
    public array $dates = [];

    public function __construct(string $provider, string $id, string $name, ?string $motto = null, ?string $location = null) {
        $this->provider = $provider; // pass provider so that the item IRI can be generated correctly
        $this->id = intval($id);
        $this->name = $name;
        // these fields may be an empty string at Hitobito, normalize to null
        if ('' != $motto) {
            $this->motto = $motto;
        }
        if ('' != $location) {
            $this->location = $location;
        }
    }
}
