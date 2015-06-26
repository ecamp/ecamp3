<?php

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;
use EcampLib\Entity\BaseEntity;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\CampOwnerRepository")
 * @ORM\Table(name="abstractCampOwner")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="entityType", type="string")
 */
abstract class AbstractCampOwner extends BaseEntity
{
    public function __construct()
    {
        parent::__construct();

        $this->ownedCamps  = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Camps, which I own myself
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Camp", mappedBy="owner")
     */
    protected $ownedCamps;

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnedCamps()
    {
        return $this->ownedCamps;
    }

    abstract public function getDisplayName();

}
