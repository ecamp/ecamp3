<?php

declare(strict_types=1);

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Entity\Camp;
use App\State\HitobitoEventCampProvider;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * The HitobitoEventCamp entity allows retrieval of the camp corresponding to a specific Hitobito event (deep-link).
 * It contains only the event id, as well as a link to the camp, which must be fetched separately.
 */
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/hitobito/{provider}/events/{eventId}/camp{._format}',
            uriVariables: [
                'provider' => new Link(parameterName: 'provider', identifiers: ['provider']),
                'eventId' => new Link(parameterName: 'eventId', identifiers: ['id']),
            ],
            openapi: new OpenApiOperation(description: 'Retrieve the Camp which was created from the specified Hitobito event.'),
            normalizationContext: ['groups' => ['read']],
            security: 'is_authenticated()',
            provider: HitobitoEventCampProvider::class,
        ),
    ],
)]
class HitobitoEventCamp {
    public string $provider;

    /**
     * The id of the Hitobito event this camp was created from.
     */
    #[ApiProperty(identifier: true, example: '1234')]
    #[Groups(['read'])]
    public int $id;

    /**
     * The camp which was created from this Hitobito event.
     */
    #[ApiProperty(writable: false)]
    #[Groups(['read'])]
    public Camp $camp;

    public function __construct(string $provider, string $id, Camp $camp) {
        $this->provider = $provider; // pass provider so that the item IRI can be generated correctly
        $this->id = intval($id);
        $this->camp = $camp;
    }
}
