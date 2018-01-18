<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="abstractCampOwner")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="entityType", type="string")
 */
abstract class AbstractCampOwner extends BaseEntity
{
    public function __construct() {
        parent::__construct();

        $this->ownedCamps = new ArrayCollection();
    }

    /**
     * @var Camp[]
     * @ORM\OneToMany(targetEntity="Camp", mappedBy="owner")
     */
    private $ownedCamps;


    /**
     * @return Camp[]
     */
    public function getOwnedCamps(): array {
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