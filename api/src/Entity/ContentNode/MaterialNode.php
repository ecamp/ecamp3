<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ContentNode;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_node_materialnode")
 * @ApiResource(routePrefix="/content_node")]
 */
class MaterialNode extends ContentNode {
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MaterialItem", mappedBy="materialNode", orphanRemoval=true, cascade={"persist"})
     */
    #[ApiProperty(readableLink: true, writableLink: false)]
    public Collection $materialItems;

    public function __construct() {
        $this->materialItems = new ArrayCollection();

        parent::__construct();
    }
}
