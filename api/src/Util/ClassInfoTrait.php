<?php

/*
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Util;

/**
 * Retrieves information about a class.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
trait ClassInfoTrait {
    /**
     * Get class name of the given object.
     */
    private function getObjectClass(object $object): string {
        return $this->getRealClassName($object::class);
    }

    /**
     * Get the real class name of a class name that could be a proxy.
     */
    private function getRealClassName(string $className): string {
        // __CG__: Doctrine Common Marker for Proxy (ODM < 2.0 and ORM < 3.0)
        $positionCg = strrpos($className, '\__CG__\\');

        if (false === $positionCg) {
            return $className;
        }

        return substr($className, $positionCg + 8);
    }
}
