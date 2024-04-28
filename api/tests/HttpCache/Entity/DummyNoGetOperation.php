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
class DummyNoGetOperation {
    /**
     * @var string
     */
    #[ORM\Column]
    public $lorem;

    /**
     * @var int The id
     */
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    public function setId($id): void {
        $this->id = $id;
    }
}
