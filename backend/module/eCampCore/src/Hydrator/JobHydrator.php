<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Job;
use Zend\Hydrator\HydratorInterface;

class JobHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var Job $job */
        $job = $object;

        return [
            'id' => $job->getId(),
            //            'camp' => $job->getCamp(),
            'name' => $job->getName(),
            // 'job_resps' =>  $job->getJobResps()
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Job $job */
        $job = $object;

        if (isset($data['name'])) {
            $job->setName($data['name']);
        }

        return $job;
    }
}
