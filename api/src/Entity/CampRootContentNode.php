<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;

/**
 * view_camp_root_content_nodes
 * For a ContentNode (only root nodes) list the camp to which it belongs.
 */
#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'view_camp_root_content_nodes')]
class CampRootContentNode {
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ContentNode::class, inversedBy: 'campRootContentNodes')]
    public ContentNode $rootContentNode;

    #[ApiProperty(
        required: false,
        openapiContext: [
            'anyOf' => [
                ['$ref' => '#/components/schemas/Camp'],
                ['type' => 'null'],
            ],
        ],
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'campRootContentNodes')]
    public Camp $camp;
}
