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
    public ?string $title = null;

    /**
     * @ORM\Column(type="text")
     */
    public string $location = '';

    /**
     * @ORM\ManyToOne(targetEntity=Camp::class, inversedBy="activities")
     * @ORM\JoinColumn(nullable=false)
     */
    public ?Camp $camp = null;

    /**
     * @ORM\OneToOne(targetEntity=ContentNode::class, mappedBy="owner", cascade={"persist", "remove"})
     */
    public $rootContentNode;

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
