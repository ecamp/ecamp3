<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
#[ApiResource]
class MaterialItem extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\ManyToOne(targetEntity="MaterialList", inversedBy="materialItems")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?MaterialList $materialList = null;

    /**
     * @ORM\ManyToOne(targetEntity="Period", inversedBy="materialItems")
     * @ORM\JoinColumn(nullable=true, onDelete="cascade")
     */
    public ?Period $period = null;

    /**
     * @ORM\ManyToOne(targetEntity="ContentNode", inversedBy="materialItems")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    public ?ContentNode $contentNode = null;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public ?string $article = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    public ?float $quantity = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $unit = null;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->materialList->getCamp();
    }

    public function setPeriod(?Period $period): void {
        $this->contentNode = null;
        $this->period = $period;
    }

    public function setContentNode(?ContentNode $contentNode): void {
        $this->period = null;
        $this->contentNode = $contentNode;
    }
}
