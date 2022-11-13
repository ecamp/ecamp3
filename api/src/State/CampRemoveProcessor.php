<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Camp;
use App\State\Util\AbstractRemoveProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CampRemoveProcessor extends AbstractRemoveProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private Security $security,
        private EntityManagerInterface $em,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Camp $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        // Deleting rootContentNode would normally be done automatically with orphanRemoval:true
        // However, this currently runs into an error due to https://github.com/doctrine-extensions/DoctrineExtensions/issues/2510

        foreach ($data->activities->getIterator() as $activity) {
            $this->em->refresh($activity->rootContentNode);
            $this->em->remove($activity->rootContentNode);
        }

        foreach ($data->categories->getIterator() as $category) {
            $this->em->refresh($category->rootContentNode);
            $this->em->remove($category->rootContentNode);
        }
    }
}
