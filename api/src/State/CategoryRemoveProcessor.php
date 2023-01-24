<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Category;
use App\State\Util\AbstractRemoveProcessor;
use Doctrine\ORM\EntityManagerInterface;

class CategoryRemoveProcessor extends AbstractRemoveProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private EntityManagerInterface $em,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Category $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        // Deleting rootContentNode would normally be done automatically with orphanRemoval:true
        // However, this currently runs into an error due to https://github.com/doctrine-extensions/DoctrineExtensions/issues/2510

        $this->em->remove($data->rootContentNode);
    }
}
