<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 */
abstract class BaseContentTypeEntity extends BaseEntity {
    /**
     * The content node to which this item belongs.
     *
     * @ORM\ManyToOne(targetEntity="ContentNode", inversedBy="content")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    public ContentNode $contentNode;
}
