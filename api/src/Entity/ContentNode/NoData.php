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
use App\Repository\NoDataRepository;
use App\State\ContentNode\ContentNodePersistProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A content node which does not store any data itself. Useful for content types which only
 * display or edit data that is stored elsewhere, e.g. on the schedule entries of the activity.
 */
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
            processor: ContentNodePersistProcessor::class
        ),
        new Delete(
            security: '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.parent !== null'
        ),
        new GetCollection(
            security: 'is_authenticated()',
            extraProperties: [
                'scoping_filters' => ['root', 'camp', 'period'],
            ]
        ),
        new Post(
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object) or object.parent === null',
            validationContext: ['groups' => ['Default', 'create']],
            processor: ContentNodePersistProcessor::class
        ),
    ],
    routePrefix: '/content_node',
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
#[ORM\Entity(repositoryClass: NoDataRepository::class)]
class NoData extends ContentNode {
    /**
     * Always null, this content node does not hold any data
     * (overridden from abstract class in order to add specific validation).
     */
    #[ApiProperty(example: null)]
    #[Groups(['read', 'write'])]
    #[Assert\IsNull]
    #[\Override]
    public function getData(): ?array {
        return parent::getData();
    }
}
