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
 * @ORM\Table(name="content_node_storyboard")
 * @ApiResource(routePrefix="/content_node")]
 */
class Storyboard extends ContentNode {
    /**
     * @ORM\OneToMany(targetEntity="StoryboardSection", mappedBy="storyboard", orphanRemoval=true, cascade={"persist"})
     */
    #[ApiProperty(readableLink: true, writableLink: false)]
    public Collection $sections;

    public function __construct() {
        $this->sections = new ArrayCollection();

        parent::__construct();
    }
}
