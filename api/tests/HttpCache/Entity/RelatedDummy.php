<?php

declare(strict_types=1);

namespace App\Tests\HttpCache\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Related Dummy.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
#[ORM\Entity]
class RelatedDummy extends BaseEntity {
    #[ORM\OneToMany(targetEntity: Dummy::class)]
    public Collection|iterable $dummies;

    public function __construct() {
        $this->dummies = new ArrayCollection();
    }
}
