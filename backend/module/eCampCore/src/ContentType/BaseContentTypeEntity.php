<?php

namespace eCamp\Core\ContentType;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\Entity\BelongsToContentNodeTreeInterface;
use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseContentTypeEntity extends BaseEntity implements BelongsToContentNodeTreeInterface {
    /**
     * @ORM\ManyToOne(targetEntity="eCamp\Core\Entity\ContentNode")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected ContentNode $contentNode;

    public function getContentNode(): ContentNode {
        return $this->contentNode;
    }

    public function setContentNode(ContentNode $contentNode): void {
        $this->contentNode = $contentNode;
    }
}
