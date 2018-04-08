<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\JobResp;
use Zend\Hydrator\HydratorInterface;

class JobRespHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object)
    {
        /** @var JobResp $jobResp */
        $jobResp = $object;
        return [
            'id' => $jobResp->getId(),
            'day' => $jobResp->getDay(),
            'user' => $jobResp->getUser()
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        /** @var JobResp $jobResp */
        $jobResp = $object;

        return $jobResp;
    }
}
