<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
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
