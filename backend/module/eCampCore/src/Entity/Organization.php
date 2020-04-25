<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="organizations")
 */
class Organization extends BaseEntity {
    public function __construct() {
        parent::__construct();

        $this->campTypes = new ArrayCollection();
    }

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var CampType[]
     * @ORM\OneToMany(targetEntity="CampType", mappedBy="organization", orphanRemoval=true)
     */
    protected $campTypes;


    public function getName() {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }


    /**
     * @return ArrayCollection
     */
    public function getCampTypes() {
        return $this->campTypes;
    }

    public function addCampType(CampType $campType): void {
        $campType->setOrganization($this);
        $this->campTypes->add($campType);
    }

    public function removeCampType(CampType $campType): void {
        $campType->setOrganization(null);
        $this->campTypes->removeElement($campType);
    }
}
