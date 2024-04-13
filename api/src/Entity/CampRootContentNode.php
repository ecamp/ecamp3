<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'view_camp_root_content_nodes')]
class CampRootContentNode {
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'rootContentNodes')]
    public Camp $camp;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ContentNode::class, inversedBy: 'rootContentNodes')]
    public ContentNode $rootContentNode;
}
