<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ActivityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActivityRepository::class)
 */
#[ApiResource]
class Activity extends BaseEntity {
    /**
     * @ORM\Column(type="text")
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="text")
     */
    private string $location = '';

    /**
     * @ORM\ManyToOne(targetEntity=Camp::class, inversedBy="activities")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Camp $camp = null;

    /**
     * @ORM\OneToOne(targetEntity=ContentNode::class, mappedBy="owner", cascade={"persist", "remove"})
     */
    private $rootContentNode;

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;

        return $this;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function setLocation(string $location): self {
        $this->location = $location;

        return $this;
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    public function setCamp(?Camp $camp): self {
        $this->camp = $camp;

        return $this;
    }

    public function getRootContentNode(): ?ContentNode {
        return $this->rootContentNode;
    }

    public function setRootContentNode(?ContentNode $rootContentNode): self {
        // unset the owning side of the relation if necessary
        if (null === $rootContentNode && null !== $this->rootContentNode) {
            $this->rootContentNode->setOwner(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $rootContentNode && $rootContentNode->getOwner() !== $this) {
            $rootContentNode->setOwner($this);
        }

        $this->rootContentNode = $rootContentNode;

        return $this;
    }
}
