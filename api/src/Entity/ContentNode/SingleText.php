<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Doctrine\Orm\Filter\IriFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Doctrine\Filter\ContentNodeCampFilter;
use App\Doctrine\Filter\ContentNodeIsRootFilter;
use App\Doctrine\Filter\ContentNodePeriodFilter;
use App\Entity\ContentNode;
use App\Repository\SingleTextRepository;
use App\State\ContentNode\SingleTextPersistProcessor;
use App\Validator\AssertJsonSchema;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or
                       is_granted("CAMP_IS_PUBLIC", object)'
        ),
        new Patch(
            denormalizationContext: ['groups' => ['write', 'update']],
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => ['Default', 'update']],
            processor: SingleTextPersistProcessor::class
        ),
        new Delete(
            security: '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.parent !== null'
        ),
        new GetCollection(
            security: 'is_authenticated()',
            parameters: [
                'contentType' => new QueryParameter(filter: new IriFilter()),
                'root' => new QueryParameter(filter: new IriFilter()),
                'camp' => new QueryParameter(filter: ContentNodeCampFilter::class),
                'period' => new QueryParameter(filter: ContentNodePeriodFilter::class),
                'isRoot' => new QueryParameter(filter: new ContentNodeIsRootFilter()),
            ],
            extraProperties: [
                'scoping_filters' => ['root', 'camp', 'period'],
            ]
        ),
        new Post(
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object) or object.parent === null',
            validationContext: ['groups' => ['Default', 'create']],
            processor: SingleTextPersistProcessor::class
        ),
    ],
    routePrefix: '/content_node',
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
#[ORM\Entity(repositoryClass: SingleTextRepository::class)]
class SingleText extends ContentNode {
    public const JSON_SCHEMA = [
        'type' => 'object',
        'additionalProperties' => false,
        'required' => ['html'],
        'properties' => [
            'html' => [
                'type' => 'string',
            ],
        ],
    ];

    public const DATA_DEFAULT = '{"html":""}';

    public function __construct() {
        parent::__construct();
        $this->data = json_decode(self::DATA_DEFAULT, true);
    }

    /**
     * Holds the actual data of the content node
     * (overridden from abstract class in order to add specific validation).
     */
    #[ApiProperty(
        default: self::DATA_DEFAULT,
        example: ['html' => 'my example text']
    )]
    #[Groups(['read', 'write'])]
    #[AssertJsonSchema(schema: self::JSON_SCHEMA)]
    #[Assert\NotNull]
    #[\Override]
    public function getData(): ?array {
        return $this->data;
    }
}
