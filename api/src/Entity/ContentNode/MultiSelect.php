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
 * @ORM\Table(name="content_node_multiselect")
 * @ApiResource(routePrefix="/content_node")]
 */
class MultiSelect extends ContentNode {
    /**
     * @ORM\OneToMany(targetEntity="MultiSelectOption", mappedBy="multiSelect", orphanRemoval=true, cascade={"persist"})
     */
    #[ApiProperty(readableLink: true, writableLink: false)]
    public Collection $options;

    public function __construct() {
        $this->options = new ArrayCollection();
    }
}
