<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ContentNode;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_node_singletext")
 * @ApiResource(routePrefix="/content_node")]
 */
class SingleText extends ContentNode {
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $text = null;
}
