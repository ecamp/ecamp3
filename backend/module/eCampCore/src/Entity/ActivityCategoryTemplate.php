<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * ActivityType.
 *
 * @ORM\Entity
 */
class ActivityCategoryTemplate extends BaseEntity {
    /**
     * @ORM\ManyToOne(targetEntity="CampTemplate")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    protected $campTemplate;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ContentTypeConfigTemplate", mappedBy="activityCategoryTemplate", orphanRemoval=true)
     */
    private $contentTypeConfigTemplates;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $short;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    private $color;

    /**
     * @var string
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $numberingStyle;

    public function __construct() {
        parent::__construct();

        $this->contentTypeConfigTemplates = new ArrayCollection();
    }

    /**
     * @return CampTemplate
     */
    public function getCampTemplate() {
        return $this->campTemplate;
    }

    public function setCampTemplate(?CampTemplate $campTemplate) {
        $this->campTemplate = $campTemplate;
    }

    /**
     * @return string
     */
    public function getShort() {
        return $this->short;
    }

    public function setShort($short) {
        $this->short = $short;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getColor() {
        return $this->color;
    }

    public function setColor($color) {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getNumberingStyle() {
        return $this->numberingStyle;
    }

    public function setNumberingStyle($numberingStyle) {
        $this->numberingStyle = $numberingStyle;
    }

    /**
     * @return ArrayCollection
     */
    public function getContentTypeConfigTemplates() {
        return $this->contentTypeConfigTemplates;
    }

    public function addContentTypeConfigTemplate(ContentTypeConfigTemplate $contentTypeConfigTemplate) {
        $contentTypeConfigTemplate->setActivityCategoryTemplate($this);
        $this->contentTypeConfigTemplates->add($contentTypeConfigTemplate);
    }

    public function removeContentTypeConfigTemplate(ContentTypeConfigTemplate $contentTypeConfigTemplate) {
        $contentTypeConfigTemplate->setActivityCategoryTemplate(null);
        $this->contentTypeConfigTemplates->removeElement($contentTypeConfigTemplate);
    }
}
