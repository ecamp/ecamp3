<?php

declare(strict_types=1);

namespace App\Tests\HttpCache\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\SerializedName;

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
    #[ORM\OneToOne(targetEntity: RelatedOwningDummy::class, inversedBy: 'ownedDummy', cascade: ['persist'])]
    public $relatedOwningDummy;

    #[ORM\ManyToMany(targetEntity: RelatedDummy::class)]
    public $manyToManyDummies;

    #[SerializedName('manyToManyDummies')]
    public function getEmbeddedManyToManyDummies() {
        return $this->manyToManyDummies;
    }

    public function getRelatedDummy(): ?RelatedDummy {
        return $this->relatedDummy;
    }

    #[SerializedName('relatedDummy')]
    public function getEmbeddedRelatedDummy(): ?RelatedDummy {
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
