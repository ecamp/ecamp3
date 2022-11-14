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
use App\Repository\MultiSelectRepository;
use App\State\ContentNode\ContentNodePersistProcessor;
use App\State\ContentNode\MultiSelectCreateProcessor;
use App\Validator\AssertJsonSchema;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'
        ),
        new Patch(
            processor: ContentNodePersistProcessor::class,
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
            processor: MultiSelectCreateProcessor::class,
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => ['Default', 'create']]
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    routePrefix: '/content_node'
)]
#[ORM\Entity(repositoryClass: MultiSelectRepository::class)]
class MultiSelect extends ContentNode {
    public const JSON_SCHEMA = [
        'type' => 'object',
        'additionalProperties' => false,
        'required' => ['options'],
        'properties' => [
            'options' => [
                'type' => 'object',
                'additionalProperties' => ['$ref' => '#/$defs/option'],
            ],
        ],
        '$defs' => [
            'option' => [
                'type' => 'object',
                'additionalProperties' => false,
                'required' => ['checked'],
                'properties' => [
                    'checked' => [
                        'type' => 'boolean',
                    ],
                ],
            ],
        ],
    ];

    /**
     * Holds the actual data of the content node
     * (overridden from abstract class in order to add specific validation).
     */
    #[ApiProperty(example: ['options' => [
        'outdoorTechnique' => ['checked' => false],
        'natureAndEnvironment' => ['checked' => true],
    ]])]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    #[Assert\IsNull(groups: ['create'])] // create with empty data; default value is populated by data persister
    #[Assert\NotNull(groups: ['update'])]
    #[AssertJsonSchema(schema: self::JSON_SCHEMA, groups: ['update'])]
    public ?array $data = null;
}
