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
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Resource linked to a standard object.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
#[ORM\Entity]
class ContainNonResource extends BaseEntity {
    /**
     * @var ContainNonResource
     */
    #[Groups('contain_non_resource')]
    public $nested;

    /**
     * @var NotAResource
     */
    #[Groups('contain_non_resource')]
    public $notAResource;
}
