<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\ContentNode;
use App\Repository\StoryboardRepository;
use App\Validator\AssertJsonSchema;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'
        ),
        new Patch(
            denormalizationContext: ['groups' => ['write', 'update']],
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => ['Default', 'update']]
        ),
        new Delete(
            security: '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.parent !== null'
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Post(
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => ['Default', 'create']]
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    routePrefix: '/content_node'
)]
#[ORM\Entity(repositoryClass: StoryboardRepository::class)]
class Storyboard extends ContentNode {
    public const JSON_SCHEMA = [
        'type' => 'object',
        'additionalProperties' => false,
        'required' => ['sections'],
        'properties' => [
            'sections' => [
                'anyOf' => [
                    [
                        'type' => 'object',
                        'patternProperties' => [
                            // uuid4 key
                            '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}' => [
                                '$ref' => '#/$defs/section',
                            ],
                        ],
                        'additionalProperties' => false,
                    ],

                    // empty array
                    // this is needed because PHP's json_decode cannot distinguish between empty array and empty object and will always decode [] to an empty array
                    [
                        'type' => 'array',
                        'maxItems' => 0,
                    ],
                ],
            ],
        ],
        '$defs' => [
            'section' => [
                'type' => 'object',
                'additionalProperties' => false,
                'required' => ['column1', 'column2', 'column3', 'position'],
                'properties' => [
                    'column1' => [
                        'type' => 'string',
                    ],
                    'column2' => [
                        'type' => 'string',
                    ],
                    'column3' => [
                        'type' => 'string',
                    ],
                    'position' => [
                        'type' => 'integer',
                        'minimum' => 0,
                    ],
                ],
            ],
        ],
    ];

    /**
     * Holds the actual data of the content node
     * (overridden from abstract class in order to add specific validation).
     */
    #[ApiProperty(example: ['sections' => []])]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    #[AssertJsonSchema(schema: self::JSON_SCHEMA)]
    public ?array $data = ['sections' => []];
}
