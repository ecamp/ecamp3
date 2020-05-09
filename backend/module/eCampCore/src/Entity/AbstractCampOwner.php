<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="entityType", type="string")
 */
abstract class AbstractCampOwner extends BaseEntity {
    /**
     * @var Camp[]
     * @ORM\OneToMany(targetEntity="Camp", mappedBy="owner")
     */
    private $ownedCamps;

    public function __construct() {
        parent::__construct();

        $this->ownedCamps = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getOwnedCamps() {
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

    /**
     * @return string
     */
    abstract public function getDisplayName();
}
