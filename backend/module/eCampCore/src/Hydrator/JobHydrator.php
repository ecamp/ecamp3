<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Job;
use Laminas\Hydrator\HydratorInterface;

class JobHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
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
     */
    public function hydrate(array $data, $object): Job {
        /** @var Job $job */
        $job = $object;

        if (isset($data['name'])) {
            $job->setName($data['name']);
        }

        return $job;
    }
}
