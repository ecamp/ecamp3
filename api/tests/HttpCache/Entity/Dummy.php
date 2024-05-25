<?php

declare(strict_types=1);

namespace App\Tests\HttpCache\Entity;

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

    /**
     * @var null|RelatedOwningDummy
     */
    #[ORM\OneToOne(targetEntity: RelatedOwningDummy::class, cascade: ['persist'], inversedBy: 'ownedDummy')]
    public $relatedOwningDummy;

    public function getRelatedDummy(): ?RelatedDummy {
        return $this->relatedDummy;
    }

    public function setRelatedDummy(RelatedDummy $relatedDummy): void {
        $this->relatedDummy = $relatedDummy;
    }

    public function getRelatedOwningDummy() {
        return $this->relatedOwningDummy;
    }

    public function setRelatedOwningDummy(RelatedOwningDummy $relatedOwningDummy): void {
        $this->relatedOwningDummy = $relatedOwningDummy;
    }
}
