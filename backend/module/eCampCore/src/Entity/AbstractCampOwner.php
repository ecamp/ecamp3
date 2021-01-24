<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="entityType", type="string")
 */
abstract class AbstractCampOwner extends BaseEntity {
    /**
     * @ORM\OneToMany(targetEntity="Camp", mappedBy="owner")
     */
    private Collection $ownedCamps;

    public function __construct() {
        parent::__construct();

        $this->ownedCamps = new ArrayCollection();
    }

    public function getOwnedCamps(): Collection {
        return $this->ownedCamps;
    }

    public function addOwnedCamp(Camp $camp): void {
        $camp->setOwner($this);
        $this->ownedCamps->add($camp);
    }

    public function removeOwnedCamp(Camp $camp): void {
        $camp->setOwner(null);
        $this->ownedCamps->removeElement($camp);
    }

    abstract public function getDisplayName(): ?string;
}
