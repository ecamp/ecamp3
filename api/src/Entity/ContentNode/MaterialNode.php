<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ContentNode;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_node_materialnode")
 * @ApiResource(routePrefix="/content_node")]
 */
class MaterialNode extends ContentNode
{
}
