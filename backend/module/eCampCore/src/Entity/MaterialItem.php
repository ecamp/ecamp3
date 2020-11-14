<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class MaterialItem extends BaseEntity implements BelongsToCampInterface {
    /**
     * @var MaterialList
     * @ORM\ManyToOne(targetEntity="MaterialList")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    protected $materialList;

    /**
     * @var Period
     * @ORM\ManyToOne(targetEntity="Period")
     * @ORM\JoinColumn(nullable=true, onDelete="cascade")
     */
    protected $period;

    /**
     * @var ActivityContent
     * @ORM\ManyToOne(targetEntity="eCamp\Core\Entity\ActivityContent")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    protected $activityContent;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $article;

    /**
     * @var string
     * @ORM\Column(type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $unit;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return MaterialList
     */
    public function getMaterialList() {
        return $this->materialList;
    }

    public function setMaterialList($materialList) {
        $this->materialList = $materialList;
    }

    /**
     * @return Camp
     */
    public function getCamp() {
        return $this->materialList->getCamp();
    }

    /**
     * @return Period
     */
    public function getPeriod() {
        return $this->period;
    }

    public function setPeriod(?Period $period) {
        $this->activityContent = null;
        $this->period = $period;
    }

    /**
     * @return ActivityContent
     */
    public function getActivityContent() {
        return $this->activityContent;
    }

    public function setActivityContent(?ActivityContent $activityContent) {
        $this->period = null;
        $this->activityContent = $activityContent;
    }

    public function getArticle() {
        return $this->article;
    }

    public function setArticle($article) {
        $this->article = $article;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function getUnit() {
        return $this->unit;
    }

    public function setUnit($unit) {
        $this->unit = $unit;
    }
}
