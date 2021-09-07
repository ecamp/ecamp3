<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\BaseEntity;
use App\Entity\SortableEntityInterface;
use App\Entity\SortableEntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_node_multiselect_option")
 * @ApiResource(routePrefix="/content_node")]
 */
class MultiSelectOption extends BaseEntity implements SortableEntityInterface
{
    use SortableEntityTrait;

    /**
     * @ORM\ManyToOne(targetEntity="MultiSelect", inversedBy="options")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[ApiProperty(readableLink: false, writableLink: false)]
    public MultiSelect $multiSelect;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public string $translateKey;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    public bool $checked = false;
}
