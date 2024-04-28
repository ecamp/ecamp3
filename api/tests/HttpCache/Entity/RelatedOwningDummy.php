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

use Doctrine\ORM\Mapping as ORM;

/**
 * Related Owning Dummy.
 *
 * @author Sergey V. Ryabov <sryabov@mhds.ru>
 */
#[ORM\Entity]
class RelatedOwningDummy extends BaseEntity {
    #[ORM\OneToOne(targetEntity: Dummy::class, cascade: ['persist'], mappedBy: 'relatedOwningDummy')]
    public ?Dummy $ownedDummy = null;

    /**
     * Get owned dummy.
     */
    public function getOwnedDummy(): Dummy {
        return $this->ownedDummy;
    }

    /**
     * Set owned dummy.
     *
     * @param Dummy $ownedDummy the value to set
     */
    public function setOwnedDummy(Dummy $ownedDummy): void {
        $this->ownedDummy = $ownedDummy;
        if ($this !== $this->ownedDummy->getRelatedOwningDummy()) {
            $this->ownedDummy->setRelatedOwningDummy($this);
        }
    }
}
