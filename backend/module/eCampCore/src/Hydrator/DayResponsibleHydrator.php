<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\DayResponsible;
use Laminas\Hydrator\HydratorInterface;

class DayResponsibleHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var DayResponsible $dayResponsible */
        $dayResponsible = $object;

        return [
            'id' => $dayResponsible->getId(),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): DayResponsible {
        // @var DayResponsible $dayResponsible
        return $object;
    }
}
