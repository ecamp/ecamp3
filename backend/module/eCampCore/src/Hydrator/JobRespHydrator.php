<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\JobResp;
use Laminas\Hydrator\HydratorInterface;

class JobRespHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var JobResp $jobResp */
        $jobResp = $object;

        return [
            'id' => $jobResp->getId(),
            //            'day' => $jobResp->getDay(),
            //            'user' => $jobResp->getUser()
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): JobResp {
        // @var JobResp $jobResp
        return $object;
    }
}
