<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SingleTextRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SingleTextRepository::class)
 * @ORM\Table(name="content_type_singletext")
 */
#[ApiResource]
class SingleText extends BaseEntity {
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $text = null;

    /**
     * @ORM\ManyToOne(targetEntity=ContentNode::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ContentNode $contentNode = null;

    public function getText(): ?string {
        return $this->text;
    }

    public function setText(?string $text): self {
        $this->text = $text;

        return $this;
    }

    public function getContentNode(): ?ContentNode {
        return $this->contentNode;
    }

    public function setContentNode(?ContentNode $contentNode): self {
        $this->contentNode = $contentNode;

        return $this;
    }
}
