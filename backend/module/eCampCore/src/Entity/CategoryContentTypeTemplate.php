<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="categoryTemplate_contentType_unique", columns={"categoryTemplateId", "contentTypeId"})
 * })
 */
class CategoryContentTypeTemplate extends BaseEntity {
    /**
     * @ORM\ManyToOne(targetEntity="CategoryTemplate")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?CategoryTemplate $categoryTemplate = null;

    /**
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ContentType $contentType = null;

    public function getCategoryTemplate(): ?CategoryTemplate {
        return $this->categoryTemplate;
    }

    public function setCategoryTemplate(?CategoryTemplate $categoryTemplate) {
        $this->categoryTemplate = $categoryTemplate;
    }

    public function getContentType(): ?ContentType {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType) {
        $this->contentType = $contentType;
    }
}
