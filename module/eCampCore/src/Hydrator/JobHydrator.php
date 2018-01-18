<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Job;
use Zend\Hydrator\HydratorInterface;

class JobHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var Job $job */
        $job = $object;
        return [
            'id' => $job->getId(),
            'camp' => $job->getCamp(),
            'name' => $job->getName(),
            // 'job_resps' =>  $job->getJobResps()
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Job $job */
        $job = $object;

        $job->setName($data['name']);

        return $job;
    }
}