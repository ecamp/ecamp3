<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class MaterialListTemplate extends BaseEntity {
    /**
     * @var CampTemplate
     * @ORM\ManyToOne(targetEntity="CampTemplate")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $campTemplate;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @return CampTemplate
     */
    public function getCampTemplate() {
        return $this->campTemplate;
    }

    /**
     * @param $campTemplate
     */
    public function setCampTemplate(?CampTemplate $campTemplate) {
        $this->campTemplate = $campTemplate;
    }

    public function getName() {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }
}
