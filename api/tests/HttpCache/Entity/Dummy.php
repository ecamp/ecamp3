<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\HttpCache\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Dummy.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
#[ORM\Entity]
class Dummy extends BaseEntity {
    #[ORM\ManyToOne(targetEntity: RelatedDummy::class)]
    public ?RelatedDummy $relatedDummy = null;

    #[ORM\ManyToMany(targetEntity: RelatedDummy::class)]
    public Collection|iterable $relatedDummies;

    /**
     * @var null|RelatedOwningDummy
     */
    #[ORM\OneToOne(targetEntity: RelatedOwningDummy::class, cascade: ['persist'], inversedBy: 'ownedDummy')]
    public $relatedOwningDummy;

    public function __construct() {
        $this->relatedDummies = new ArrayCollection();
    }

    public function getRelatedDummy(): ?RelatedDummy {
        return $this->relatedDummy;
    }

    public function setRelatedDummy(RelatedDummy $relatedDummy): void {
        $this->relatedDummy = $relatedDummy;
    }

    public function addRelatedDummy(RelatedDummy $relatedDummy): void {
        $this->relatedDummies->add($relatedDummy);
    }

    public function getRelatedOwningDummy() {
        return $this->relatedOwningDummy;
    }

    public function setRelatedOwningDummy(RelatedOwningDummy $relatedOwningDummy): void {
        $this->relatedOwningDummy = $relatedOwningDummy;
    }

    public function getRelatedDummies(): Collection|iterable {
        return $this->relatedDummies;
    }
}
