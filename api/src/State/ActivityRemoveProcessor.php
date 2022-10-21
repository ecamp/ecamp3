<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;

final class ActivityRemoveProcessor implements ProcessorInterface {
    public function __construct(
        private ProcessorInterface $decorated,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @param Activity $data
     *
     * @return Activity
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []) {
        $data = $this->beforeRemove($data);

        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }

    /**
     * @param Activity $data
     */
    public function beforeRemove($data): ?Activity {
        // Deleting rootContentNode would normally be done automatically with orphanRemoval:true
        // However, this currently runs into an error due to https://github.com/doctrine-extensions/DoctrineExtensions/issues/2510
        $this->em->remove($data->rootContentNode);

        return $data;
    }
}
