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
 * @ORM\Table(name="content_node_storyboard_section")
 * @ApiResource(routePrefix="/content_node")]
 */
class StoryboardSection extends BaseEntity implements SortableEntityInterface {
    use SortableEntityTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Storyboard", inversedBy="sections")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[ApiProperty(readableLink: false, writableLink: false)]
    public Storyboard $storyboard;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $column1 = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $column2 = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string  $column3 = null;
}
