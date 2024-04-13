<?php

namespace App\Entity;

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

    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'campRootContentNodes')]
    public Camp $camp;
}
