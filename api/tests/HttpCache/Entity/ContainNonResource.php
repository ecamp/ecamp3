<?php

declare(strict_types=1);

namespace App\Tests\HttpCache\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Resource linked to a standard object.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
#[ORM\Entity]
class ContainNonResource extends BaseEntity {
    /**
     * @var NotAResource
     */
    public $notAResource;

    /**
     * @var NotAResource[]
     */
    public $collectionOfNotAResource;

    public ?RelatedDummy $relatedDummy = null;
}
