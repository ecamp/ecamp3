<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Medium;
use Zend\Hydrator\HydratorInterface;

class MediumHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object)
    {
        /** @var Medium $medium */
        $medium = $object;
        return [
            'name' => $medium->getName(),
            'default' => $medium->isDefault()
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        /** @var Medium $medium */
        $medium = $object;

        $medium->setDefault($data['default']);

        return $medium;
    }
}
