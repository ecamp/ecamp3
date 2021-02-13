<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    protected ?CampTemplate $campTemplate = null;

    /**
     * @ORM\OneToMany(targetEntity="ContentTypeConfigTemplate", mappedBy="activityCategoryTemplate", orphanRemoval=true)
     */
    private Collection $contentTypeConfigTemplates;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private ?string $short = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    private ?string $color = null;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private ?string $numberingStyle = null;

    public function __construct() {
        parent::__construct();

        $this->contentTypeConfigTemplates = new ArrayCollection();
    }

    public function getCampTemplate(): ?CampTemplate {
        return $this->campTemplate;
    }

    public function setCampTemplate(?CampTemplate $campTemplate) {
        $this->campTemplate = $campTemplate;
    }

    public function getShort(): ?string {
        return $this->short;
    }

    public function setShort(?string $short) {
        $this->short = $short;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name) {
        $this->name = $name;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(?string $color) {
        $this->color = $color;
    }

    public function getNumberingStyle(): ?string {
        return $this->numberingStyle;
    }

    public function setNumberingStyle(?string $numberingStyle) {
        $this->numberingStyle = $numberingStyle;
    }

    public function getContentTypeConfigTemplates(): Collection {
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
