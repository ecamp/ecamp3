<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="category_contentType_unique", columns={"categoryId", "contentTypeId"})
 * })
 */
class CategoryContentType extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Category $category = null;

    /**
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ContentType $contentType = null;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $categoryContentTypeTemplateId = null;

    public function getCamp(): ?Camp {
        return (null != $this->category) ? $this->category->getCamp() : null;
    }

    public function getCategory(): ?Category {
        return $this->category;
    }

    public function setCategory(?Category $category): void {
        $this->category = $category;
    }

    public function getContentType(): ?ContentType {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType): void {
        $this->contentType = $contentType;
    }

    public function getCategoryContentTypeTemplateId(): ?string {
        return $this->categoryContentTypeTemplateId;
    }

    public function setCategoryContentTypeTemplateId(?string $categoryContentTypeTemplateId): void {
        $this->categoryContentTypeTemplateId = $categoryContentTypeTemplateId;
    }
}
