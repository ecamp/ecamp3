<?php

declare(strict_types=1);

namespace App\Tests\HttpCache\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;

/**
 * DummyNoGetOperation.
 *
 * @author Grégoire Hébert gregoire@les-tilleuls.coop
 */
#[ApiResource(operations: [new Put(), new Post()])]
#[ORM\Entity]
class DummyNoGetOperation extends BaseEntity {
    /**
     * @var string
     */
    #[ORM\Column]
    public $lorem;
}
